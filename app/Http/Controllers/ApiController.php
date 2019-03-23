<?php


namespace App\Http\Controllers;

use bunq\Context\ApiContext;
use bunq\Context\BunqContext;
use bunq\Exception\BunqException;
use bunq\Model\Generated\Endpoint\BunqMeFundraiserProfile;
use bunq\Model\Generated\Endpoint\MonetaryAccountBank;
use bunq\Model\Generated\Endpoint\RequestInquiry;
use bunq\Model\Generated\Object\Amount;
use bunq\Model\Generated\Object\Pointer;
use Illuminate\Http\Request;
use GoogleCloudVision\GoogleCloudVision;
use GoogleCloudVision\Request\AnnotateImageRequest;
use Aws\S3\S3Client;

//use Bunq

class ApiController extends Controller
{
    protected $S3Client;
    protected $BunqClient;
    protected $APIKey;
    protected $environment;

    const POINTER_TYPE_EMAIL = 'EMAIL';
    const POINTER_TYPE_PHONE_NUMBER = 'PHONE_NUMBER';
    const POINTER_TYPE_IBAN = 'IBAN';
    const CARD_PIN_ASSIGNMENT_PRIMARY = 'PRIMARY';
    const NOTIFICATION_DELIVERY_METHOD_URL = 'URL';
    const NOTIFICATION_CATEGORY_MUTATION = 'MUTATION';

    /**
     * ApiController constructor.
     */
    public function __construct()
    {
        $fileName = base_path('bunq.conf');
        $apiContext = ApiContext::restore($fileName);
        BunqContext::loadApiContext($apiContext);
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

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAccounts()
    {
        $bankAccounts = MonetaryAccountBank::listing()->getValue();
        return response()->json($bankAccounts);
    }


    /**
     * @param string $amount
     * @param string $recipientValueString
     * @param string $recipientNameString
     * @param string $description
     * @param null $monetaryAccount
     * @return \Illuminate\Http\JsonResponse
     * @throws BunqException
     */
    public function createPayments(Request $request)
    {
        //$amount = '20', $recipientValueString = 'pimmink@icloud.com', $recipientNameString = 'Pim Immink', $description = 'Voor het bonnetje', $monetaryAccount = null)
//        return $request;
//        $request = RequestInquiry::create(
//            new Amount($amount, 'EUR'),
//            $this->determinePointerFromRecipient($recipientValueString, $recipientNameString),
//            $description,
//            true,
//            MonetaryAccountBank::listing()->getValue()[0]->getId()
//        )->getValue();

        $request = BunqMeFundraiserProfile::createFromJsonString('{"amount":{"currency":"EUR","value":"7.00"}}')->getStatus();
        return response()->json($request);
    }

    /**
     * @param string $recipientValueString
     * @param string|null $recipientName
     *
     * @return Pointer
     * @throws BunqException
     */
    public function determinePointerFromRecipient(string $recipientValueString, string $recipientName = null): Pointer
    {
        if (filter_var($recipientValueString, FILTER_VALIDATE_EMAIL)) {
            $pointer = new Pointer(self::POINTER_TYPE_EMAIL, $recipientValueString);
        } elseif (preg_match(self::REGEX_E164_PHONE, $recipientValueString) === self::PREG_MATCH_SUCCESS) {
            $pointer = new Pointer(self::POINTER_TYPE_PHONE_NUMBER, $recipientValueString);
        } elseif (!is_null($recipientName)) {
            $pointer = new Pointer(self::POINTER_TYPE_IBAN, $recipientValueString, $recipientName);
        } else {
            throw new BunqException(vsprintf(self::ERROR_COULD_NOT_DETERMINE_RECIPIENT_TYPE, [$recipientValueString]));
        }

        return $pointer;
    }

    /**
     * @param $hash
     * @return mixed
     */
    public function getJSON($hash)
    {
        $result = $this->getS3($this->createTextKey($hash));

        return $result['Body'];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createReceipt(Request $request)
    {
        $hash = $this->generateHash();

        $image = $request->receipt;

        $base64Image = base64_encode(file_get_contents($image));

        $request = new AnnotateImageRequest();
        $request->setImage($base64Image);
        $request->setFeature("DOCUMENT_TEXT_DETECTION");
        $gcvRequest = new GoogleCloudVision([$request], env('GOOGLE_CLOUD_KEY'));
        //send annotation request
        $response = $gcvRequest->annotate();


        $this->storeS3File($this->createImageKey($hash), $image, 'image/png');
        $this->storeS3Object($this->createTextKey($hash), json_encode($response), 'application/json');

        return response()->json([
            'hash' => $hash
        ]);
    }

    /**
     * @param $key
     * @return \Aws\Result
     */
    public function getS3($key)
    {
        $result = $this->S3Client->getObject([
            'Bucket' => env('BUCKET_NAME'),
            'Key' => $key,
        ]);

        return $result;
    }

    /**
     * @param $key
     * @param $body
     * @param $mime
     * @return \Aws\Result
     */
    public function storeS3Object($key, $body, $mime)
    {
        $result = $this->S3Client->putObject([
            'Bucket' => env('BUCKET_NAME'),
            'Key' => $key,
            'Body' => $body,
            'ContentType' => $mime,
            'ACL' => 'public-read'
        ]);

        return $result;
    }

    /**
     * @param $key
     * @param $file
     * @param $mime
     * @return \Aws\Result
     */
    public function storeS3File($key, $file, $mime)
    {
        $result = $this->S3Client->putObject([
            'Bucket' => env('BUCKET_NAME'),
            'Key' => $key,
            'SourceFile' => $file,
            'ContentType' => $mime,
            'ACL' => 'public-read'
        ]);

        return $result;
    }

    /**
     * @param $hash
     * @return string
     */
    public function createImageKey($hash)
    {
        return $hash . '.png';
    }

    /**
     * @param $hash
     * @return string
     */
    public function createTextKey($hash)
    {
        return $hash . '.json';
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function generateHash()
    {
        return bin2hex(random_bytes(16));
    }

}
