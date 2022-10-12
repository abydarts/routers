<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Trans;
use Askync\Utils\Facades\AskyncResponse;
use Askync\Utils\HttpUtils\Client;
use Askync\Utils\Utils\ResponseException;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function actionMystery()
    {
        $clientPass = $this->request->header('x-client-pass', -1);
        if(-1 == $clientPass || $clientPass != env('CLIENT_HEADER_PASSWORD')) {
            throw new ResponseException('Unauthorized', AskyncResponse::ERROR_UNAUTHORIZED);
        }

        $sanitized = $this->validate($this->request,[
            'signature' => 'required',
            'reference_no' => 'required',
            'invoice_no' => 'required',
            'customer_name' => 'required',
            'customer_phone' => 'required',
            'customer_email' => 'required',
            'amount' => 'required',
            'payment_method_code' => 'required',
            'pay_code' => '',
            'currency' => 'required',
            'description' => 'required',
            'created_at' => 'required',
            'paid_at' => 'required',
            'category' => 'string'
        ]);

        if($transaction = Trans::where('trans_id', $sanitized['reference_no'])->first() )
        {
            if(!$transaction->response) {
                $post = (new \GuzzleHttp\Client(['http_errors' => false]))->post(
                    $transaction->request['header']['url'],
                    [
                        'headers' => ['X-Auth-Sec' => $transaction->request['header']['headers']['X-Auth-Sec']],
                        'form_params' => $transaction->request['data']
                    ]
                );
                $postHeader = $post->getHeaders();
                $postBody = $post->getBody()->getContents();
                $post = json_decode($post->getBody()->getContents(), true);
                $log = new Log();
                $log->direction = 'OUT';
                $log->method = 'POST';
                $log->request = [
                    'header' => array_merge([
                        'headers' => ['X-Auth-Sec' => $transaction->request['header']['headers']['X-Auth-Sec']],
                        'form_params' => $sanitized
                    ], ['url' => $transaction->request['header']['url']]),
                    'data' => $sanitized,
                ];

                $log->response = [
                    'header' => $postHeader,
                    'data' => ($json=json_decode($postBody, true)) ? $json : ['content' => $postBody]
                ];
                $log->save();
            }
            return AskyncResponse::fail(AskyncResponse::ERROR_UNPROCESSABLE_ENTITY, 'Transaction was recorded at '. $transaction->target_store );
        }

        $targets = config('vendor');
        $target = $targets[array_rand($targets)];
        if(isset($sanitized['category'])) {
            $tgs = [];
            foreach ($targets as &$t) {
                if ($t['enabled'] === 1) {
                    if (isset($sanitized['category'])) {
                        if ($sanitized['category'] == $t['category']) {
                            $tgs[] = $t;
                        }
                    } else {
                        $tgs[] = $t;
                    }
                }

            }
            if (count($tgs) < 1) {
                return AskyncResponse::fail(AskyncResponse::ERROR_UNPROCESSABLE_ENTITY, 'No store available to process this request, pls check config or category');
            }

            $target = $tgs[array_rand($tgs)];
        }

        $post = (new \GuzzleHttp\Client(['http_errors' => false, 'timeout' => 180]))->post(
            $target['mbox_url'],
            [
                'headers' => ['X-Auth-Sec' => $target['mbox_header_auth']],
                'form_params' => $sanitized
            ]
        );

        $transaction = new Trans();
        $transaction->trans_id = $sanitized['reference_no'];
        $transaction->target_store = $target['name'];
        $transaction->request = [
            'header' => array_merge([
                'headers' => ['X-Auth-Sec' => $target['mbox_header_auth']],
                'form_params' => $sanitized
            ], ['url' => $target['mbox_url']]),
            'data' => $sanitized,
        ];



        $postHeader = $post->getHeaders();
        $postBody = $post->getBody()->getContents();
        $postArr = json_decode($postBody, true);

        $transaction->save();
        if( isset($postArr['error']) && $postArr['error'] == 0 ) {
            $transaction->response = [
                'header' => $postHeader,
                'data' => ( $json=json_decode($postBody, true) ) ? $json : ['content' => $postBody]
            ];

        } else {
            \Illuminate\Support\Facades\Log::info( sprintf('%s : %s', $transaction->id, $postBody) );
            $transaction->response = [
                'header' => [],
                'data' => ['content' => $postBody]
            ];
        }
        $transaction->save();

        $log = new Log();
        $log->direction = 'OUT';
        $log->method = 'POST';
        $log->request = [
            'header' => array_merge([
                'headers' => ['X-Auth-Sec' => $target['mbox_header_auth']],
                'form_params' => $sanitized
            ], ['url' => $target['mbox_url']]),
            'data' => $sanitized,
        ];

        $log->response = [
            'header' => $postHeader,
            'data' => ($json=json_decode($postBody, true)) ? $json : ['content' => $postBody]
        ];
        $log->save();

        if(!isset($postArr['error']) || $postArr['error'] != 0 ) {
            return AskyncResponse::fail(AskyncResponse::ERROR_UNPROCESSABLE_ENTITY, "[".$target['name']."]: ".((isset($postArr['description'])) ? $postArr['description'] : 'Failed') );
        }
        return AskyncResponse::success($postArr['data']);

    }
    
    public function getCategory()
    {
        $targets = config('vendor');
        $categories = [];
        foreach ($targets as &$t) {
            if($t['enabled'] === 1) {
                $categories[] = $t['category'];
            }
        }
        
        return AskyncResponse::success(array_values(array_unique($categories)), 'ok');
    }
}
