<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;

class ApiController extends Controller
{
    protected $visionClient;

    public function __construct()
    {
        $this->visionClient = new ImageAnnotatorClient();
        putenv('GOOGLE_APPLICATION_CREDENTIALS' . base_path('google-cloud-credentials.json'));
    }

    public function receipt(Request $request)
    {
        $blobData = $request->receipt;
        $response = $this->visionClient->textDetection($blobData);
        $texts = $response->getTextAnnotations();

        return $texts;

    }


//function (Request $request) {
//    \Log::debug($request);
//    return 'test';
//});
}
