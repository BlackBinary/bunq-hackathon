<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GoogleCloudVision\GoogleCloudVision;
use GoogleCloudVision\Request\AnnotateImageRequest;

class ApiController extends Controller
{
    protected $visionClient;
    protected $projectId = 'bunq-hackathon';

    public function __construct()
    {
    }

    public function receipt(Request $request)
    {
        $image = base64_encode(file_get_contents($request->receipt));

        $request = new AnnotateImageRequest();
        $request->setImage($image);
        $request->setFeature("TEXT_DETECTION");
        $gcvRequest = new GoogleCloudVision([$request],  env('GOOGLE_CLOUD_KEY'));
        //send annotation request
        $response = $gcvRequest->annotate();

        return response()->json(["description" => $response->responses[0]->textAnnotations]);

    }


//function (Request $request) {
//    \Log::debug($request);
//    return 'test';
//});
}
