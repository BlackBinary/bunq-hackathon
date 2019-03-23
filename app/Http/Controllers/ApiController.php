<?php


namespace App\Http\Controllers;

use finfo;
use Illuminate\Http\Request;
use GoogleCloudVision\GoogleCloudVision;
use GoogleCloudVision\Request\AnnotateImageRequest;
use Aws\S3\S3Client;

class ApiController extends Controller
{
    protected $S3Client;

    public function __construct()
    {
        $this->S3Client = new S3Client([
            'version' => 'latest',
            'region' => env('S3_REGION'),
            'endpoint' => env('S3_ENDPOINT'),
            'credentials' => [
                'key' => env('S3_ACCESS_KEY'),
                'secret' => env('S3_ACCESS_SECRET')
            ],
        ]);
    }

    public function getReceipt($hash)
    {
        \Log::debug($this->getS3($this->createImageKey($hash)));
        return response()->json([
            'image' => $this->getS3($this->createImageKey($hash))['body'],
            'text' => $this->getS3($this->createTextKey($hash))['body']
        ]);
    }

    public function createReceipt(Request $request)
    {
        $hash = $this->generateHash();

        $image = base64_encode(file_get_contents($request->receipt));

        return mime_content_type(file_get_contents($request->receipt));


        $request = new AnnotateImageRequest();
        $request->setImage($image);
        $request->setFeature("TEXT_DETECTION");
        $gcvRequest = new GoogleCloudVision([$request], env('GOOGLE_CLOUD_KEY'));
        //send annotation request
        $response = $gcvRequest->annotate();


        $this->storeS3($this->createImageKey($hash), $image);
        $this->storeS3($this->createTextKey($hash), json_encode($response));

        return response()->json([
            'hash' => $hash
        ]);
    }

    public function getS3($key)
    {
        $result = $this->S3Client->getObject([
            'Bucket' => env('BUCKET_NAME'),
            'Key' => $key,
        ]);

        return $result;
    }

    public function storeS3($key, $body)
    {
        $result = $this->S3Client->putObject([
            'Bucket' => env('BUCKET_NAME'),
            'Key' => $key,
            'Body' => $body,
            'ACL' => 'public-read'
        ]);

        return $result;
    }

    public function createImageKey($hash)
    {
        return $hash . '.image';
    }

    public function createTextKey($hash)
    {
        return $hash . '.text';
    }

    public function generateHash()
    {
        return bin2hex(random_bytes(16));
    }

    public function getMimeType($blob)
    {
        $finfo = new finfo(FILEINFO_MIME);
        $mimeType = $finfo->buffer($blob);
        return $mimeType;
    }

}
