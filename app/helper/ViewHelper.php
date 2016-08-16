<?php

class ViewHelper {

    static function getSelectHtml($itemId, $itemName, $itemList, $searchId ){
        $selectHtml = '';
        for($i = 0; $i < count($itemList); $i++){
            $item = $itemList[$i];
            if ( $item[$itemId] == $searchId ) {
                $selectHtml .= "<option value='" . $item[$itemId] . "' selected>" . $item[$itemName] . '</option>';
            } else{
                $selectHtml .= "<option value='" . $item[$itemId] . "'>" . $item[$itemName] . '</option>';
            }
        }
        $selectHtml .= '</select>';

        return $selectHtml;

    }

}

