<?php
/**
 * Created by IntelliJ IDEA.
 * User: Owen
 * Date: 2016/12/15
 * Time: 上午12:51
 */

namespace App\Http\Traits;
use Log;

use App\Repositories\CatalogsRepository;
use App\Transformers\CatalogsTransformer;
use App\Validators\CatalogsValidator;


trait CatalogsTrait
{
    public function getCatalogsbyName($name,CatalogsRepository $catalogsRepository,CatalogsTransformer $catalogsTransformer)
    {
        $match = false;
        $catalogs = $catalogsTransformer->transform2($catalogsRepository->all());
        foreach ($catalogs as $k => $catalog)
        {
            $keywords = explode('|',$catalog['keyword']);
            foreach ($keywords as $keyword)
            {
                if(!empty($keyword))
                {
                    if(preg_match('/'.$keyword.'/i', $name))
                    {
                        $match = $catalog['name'].'( '.$catalog['shortcode'].' )';
                        break;
                    }
                }
            }
            if($match)
            {
                break;
            }
        }
        if($match)
        {
            return $match;
        }
        else
        {
            return '其他( OTHER )';
        }
    }
}