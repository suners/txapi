<?php

namespace txapi\controllers;

!defined('ROOT') AND exit('Access Denied!');

include ROOT . '/model/DB.php';

/**
 * BaseController
 * @author abin <rawuzebin@126.com>
 */
class BaseController
{

    public $_params;

    public function response($code, $content)
    {
        if (empty($code)) {
            return false;
        }
        $data = array(
            'code' => $code
        );

        if (is_array($content)) {
            $data['content'] = $content;
        } else {
            $data['message'] = $content;
        }

        $jsonstr = isset($this->_params['callback']) ? $this->_params['callback'] . '(' . json_encode($data) . ')' : json_encode($data);

        echo $jsonstr;
        die();
    }

}
