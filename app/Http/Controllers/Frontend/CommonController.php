<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use EasyWeChat\Foundation\Application;
use Carbon\Carbon;
use Cache;
use Log;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;

use App\Repositories\AddressRepository;
use App\Repositories\CatalogsRepository;
use App\Repositories\DetailRepository;
use App\Repositories\FeedbackRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\LogisticsRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PayRepository;
use App\Repositories\ProductsRepository;
use App\Repositories\SceneRepository;
use App\Repositories\StockRepository;
use App\Repositories\UsersRepository;

use App\Transformers\AddressTransformer;
use App\Transformers\CatalogsTransformer;
use App\Transformers\DetailTransformer;
use App\Transformers\FeedbackTransformer;
use App\Transformers\InvoiceTransformer;
use App\Transformers\OrderTransformer;
use App\Transformers\PayTransformer;
use App\Transformers\ProductsTransformer;
use App\Transformers\SceneTransformer;
use App\Transformers\StockTransformer;
use App\Transformers\UsersTransformer;

use App\Validators\AddressValidator;
use App\Validators\CatalogsValidator;
use App\Validators\DetailValidator;
use App\Validators\FeedbackValidator;
use App\Validators\InvoiceValidator;
use App\Validators\LogisticsValidator;
use App\Validators\OrderValidator;
use App\Validators\PayValidator;
use App\Validators\ProductsValidator;
use App\Validators\SceneValidator;
use App\Validators\StockValidator;
use App\Validators\UsersValidator;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class CommonController extends Controller
{

    /**
     * @var UsersRepository
     */
    protected $userRepository;
    protected $userValidator;
    protected $UsersTransformer;

    protected $AddressRepository;
    protected $AddressValidator;
    protected $AddressTransformer;

    protected $CatalogsRepository;
    protected $CatalogsValidator;
    protected $CatalogsTransformer;

    protected $DetailRepository;
    protected $DetailValidator;
    protected $DetailTransformer;

    protected $FeedbackRepository;
    protected $FeedbackValidator;
    protected $FeedbackTransformer;

    protected $InvoiceRepository;
    protected $InvoiceValidator;
    protected $InvoiceTransformer;

    protected $LogisticsRepository;
    protected $LogisticsValidator;
    protected $LogisticsTransformer;

    protected $OrderRepository;
    protected $OrderValidator;
    protected $OrderTransformer;

    protected $PayRepository;
    protected $PayValidator;
    protected $PayTransformer;

    protected $ProductsRepository;
    protected $ProductsValidator;
    protected $ProductsTransformer;

    protected $SceneRepository;
    protected $SceneValidator;
    protected $SceneTransformer;

    protected $StockRepository;
    protected $StockValidator;
    protected $StockTransformer;

    protected $currentUser;
    protected $wechat;

    public function __construct(
        Application $wechat,
        UsersRepository $userRepository,
        UsersValidator $userValidator,
        UsersTransformer $usersTransformer,

        AddressRepository $addressRepository,
        AddressValidator $addressValidator,
        AddressTransformer $addressTransformer,

        CatalogsRepository $catalogsRepository,
        CatalogsValidator $catalogsValidator,
        CatalogsTransformer $catalogsTransformer,

        DetailRepository $detailRepository,
        DetailValidator $detailValidator,
        DetailTransformer $detailTransformer,

        FeedbackRepository $feedbackRepository,
        FeedbackValidator $feedbackValidator,
        FeedbackTransformer $feedbackTransformer,

        InvoiceRepository $invoiceRepository,
        InvoiceValidator $invoiceValidator,
        InvoiceTransformer $invoiceTransformer,

        LogisticsRepository $logisticsRepository,
        LogisticsValidator $logisticsValidator,
        LogisticsValidator $logisticsTransformer,

        OrderRepository $orderRepository,
        OrderValidator $orderValidator,
        OrderTransformer $orderTransformer,

        PayRepository $payRepository,
        PayValidator $payValidator,
        PayTransformer $payTransformer,

        ProductsRepository $productsRepository,
        ProductsValidator $productsValidator,
        ProductsTransformer $productsTransformer,

        SceneRepository $sceneRepository,
        SceneValidator $sceneValidator,
        SceneTransformer $sceneTransformer,

        StockRepository $stockRepository,
        StockValidator $stockValidator,
        StockTransformer $stockTransformer,
        Request $request)
    {
        $this->UsersRepository = $userRepository;
        $this->UsersValidator  = $userValidator;
        $this->UsersTransformer = $usersTransformer;

        $this->AddressRepository = $addressRepository;
        $this->AddressValidator = $addressValidator;
        $this->AddressTransformer = $addressTransformer;

        $this->CatalogsRepository = $catalogsRepository;
        $this->CatalogsValidator = $catalogsValidator;
        $this->CatalogsTransformer = $catalogsTransformer;

        $this->DetailRepository = $detailRepository;
        $this->DetailValidator = $detailValidator;
        $this->DetailTransformer = $detailTransformer;

        $this->FeedbackRepository = $feedbackRepository;
        $this->FeedbackValidator = $feedbackValidator;
        $this->FeedbackTransformer = $feedbackTransformer;

        $this->InvoiceRepository = $invoiceRepository;
        $this->InvoiceValidator = $invoiceValidator;
        $this->InvoiceTransformer = $invoiceTransformer;

        $this->LogisticsRepository = $logisticsRepository;
        $this->LogisticsValidator = $logisticsValidator;
        $this->LogisticsValidator = $logisticsTransformer;

        $this->OrderRepository = $orderRepository;
        $this->OrderValidator = $orderValidator;
        $this->OrderTransformer = $orderTransformer;

        $this->PayRepository = $payRepository;
        $this->PayValidator = $payValidator;
        $this->PayTransformer = $payTransformer;

        $this->ProductsRepository = $productsRepository;
        $this->ProductsValidator = $productsValidator;
        $this->ProductsTransformer = $productsTransformer;

        $this->SceneRepository = $sceneRepository;
        $this->SceneValidator = $sceneValidator;
        $this->SceneTransformer = $sceneTransformer;

        $this->StockRepository = $stockRepository;
        $this->StockValidator = $stockValidator;
        $this->StockTransformer = $stockTransformer;

        $this->currentUser = $userRepository->findByField('openid',session('wechat.oauth_user')['id'])[0];
        $this->session = session('wechat.oauth_user');
        $this->wechat = $wechat;
    }

    public function removeArg($Args,array $fields)
    {
        if(is_array($fields))
        {
            foreach ($fields as $field)
            {
                unset($Args[$field]);
            }
        }
        else
        {
            unset($Args[$fields]);
        }
        return $Args;
    }

    public function openidToid($openid)
    {
        return $this->userRepository->findByField('openid', $openid,['id'])[0]['id'];
    }

    public  function toArray($obj)
    {
        $new = array();
        foreach ($obj as $key => $val) {
            $new[$key] = $val;
        }
        return $new;
    }

    public function uuid()
    {
        try {
            return str_replace('-','',(string) Uuid::uuid4());
        } catch (UnsatisfiedDependencyException $e) {
            return false;
        }
    }
}
