<?php


namespace App\classes;
use Bitrix\Main\Page\Asset;
\CModule::IncludeModule('main');
\CModule::IncludeModule('iblock');

class Helper
{
    protected static function requireCss()
    {
        $links = require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/common/settings/cssArray.php';

        foreach($links as $link)
        {
            Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . $link, true);
        }
    }

    protected static function requireJs()
    {
        $links = require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/common/settings/jsArray.php';

        foreach($links as $link)
        {
            Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . $link, true);
        }
    }

    protected static function getMeta()
    {
        $links = require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/common/settings/meta.php';
        foreach($links as $link)
        {
            Asset::getInstance()->addString($link);
        }
    }

    public static function getSettingsHead()
    {
        global $APPLICATION;
        $APPLICATION->ShowHead();
        self::getMeta();
        self::requireCss();
        \CJSCore::Init(['jquery3']);
        self::requireJs();
        $APPLICATION->ShowPanel();
    }

    public static function simpleMenuTwoLevel($result)
    {
        $reMenu = [];

        foreach($result as $menu)
        {
            if($menu['DEPTH_LEVEL'] == 1)
            {
                $reMenu[] = $menu;
            }
            else
            {
                $reMenu[end(array_keys($reMenu))]['SUBMENU'][] = $menu;
            }
        }
        return $reMenu;
    }

    public static function getVideo($idIBlock)
    {
        $video = [];
        $res = [];
        $selectVideo = \CIBlockElement::GetList([], ['IBLOCK_ID' => $idIBlock], false, false, ['PROPERTY_VIDEO']);

        while($select = $selectVideo->Fetch())
        {
            $video[\CFile::GetByID($select['PROPERTY_VIDEO_VALUE'])->Fetch()['ID']] = \CFile::GetByID($select['PROPERTY_VIDEO_VALUE'])->Fetch();
            $video[$select['PROPERTY_VIDEO_VALUE']]['src'] = \CFile::GetPath($select['PROPERTY_VIDEO_VALUE']);
        }
        foreach($video as $movie)
        {
            $res[] = $movie;
        }

        return $res;
    }

    public static function getAllElements($iblockId, $select = [], $sort = [])
    {
        $elements = [];

        if(!is_array($iblockId))
        {
            $selectAllELements = \CIBlockElement::GetList($sort, ['IBLOCK_ID' => $iblockId], false, false, $select);
        }
        else
        {
            $selectAllELements = \CIBlockElement::GetList($sort, $iblockId, false, false, $select);
        }

        while($selects = $selectAllELements->Fetch())
        {
            $elements[] = $selects;
        }

        return $elements;
    }

    public static function getElement($idElement, $data = [])
    {
        $res = [];

        if(is_array($idElement))
        {
            $res = \CIBlockElement::GetList([], $idElement, false, false, $data)->Fetch();
        }
        else
        {
            $res = \CIBlockElement::GetList([], ['ID' => $idElement], false, false, $data)->Fetch();
        }

        return $res;
    }

    public static function getElementFields($idElement = [], $data = [])
    {
        $res = [];
        if(!is_array($idElement))
        {
            $selectElement = \CIBlockElement::GetList([], ['ID' => $idElement], false, false, $data);

            while($select = $selectElement->GetNextElement())
            {
                $res = $select->GetFields();
            }
        }
        else
        {
            $selectElement = \CIBlockElement::GetList([], $idElement, false, false, $data);

            while($select = $selectElement->GetNextElement())
            {
                $res = $select->GetFields();
            }
        }

        return $res;
    }

    public static function getElementProperties($idElement, $data = [])
    {
        $res = [];
        if(!is_array($idElement))
        {
            $selectElement = \CIBlockElement::GetList([], ['ID' => $idElement], false, false, $data);

            while($select = $selectElement->GetNextElement())
            {
                $res = $select->GetProperties();
            }
        }
        else
        {
            $selectElement = \CIBlockElement::GetList([], $idElement, false, false, $data);

            while($select = $selectElement->GetNextElement())
            {
                $res = $select->GetProperties();
            }
        }

        return $res;
    }

    public static function getSections($iblockId, $select = [])
    {
        $sections = [];

        $selectSections = \CIBlockSection::GetList([], ['IBLOCK_ID' => $iblockId], false, $select);

        while($select = $selectSections->Fetch())
        {
            $sections[] = $select;
        }

        return $sections;
    }


    public static function getImage($id, $width= false, $height = false, $resize = BX_RESIZE_IMAGE_PROPORTIONAL_ALT)
    {
        return \CFile::ResizeImageGet($id, ['width' => $width, 'height' => $height], $resize)['src'];
    }

    public static function getPropertyEnumId($iblockId = '', $data = '')
    {
        $result = [];

        if(!empty($data))
        {
            $result = \CIBlockPropertyEnum::GetList([], ['IBLOCK_ID' => $iblockId, 'EXTERNAL_ID' => $data])->Fetch();
        }
        else
        {
            $select = \CIBlockPropertyEnum::GetList([], ['IBLOCK_ID' => $iblockId]);

            while($res = $select->Fetch())
            {
                $result[] = $res;
            }
        }
        return $result;
    }

    public static function isMobile()
    {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }
}