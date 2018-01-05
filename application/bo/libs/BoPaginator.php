<?php
/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2018/1/5
 * Time: 上午8:58
 */

namespace app\bo\libs;


use think\paginator\driver\Bootstrap;

class BoPaginator extends Bootstrap
{

    public function getBaseUrl()
    {
        $parameters = [];
        if (strpos($this->options['path'], '[PAGE]') === false) {
            $path       = $this->options['path'];
        } else {
            $path       = str_replace('[PAGE]', $page, $this->options['path']);
        }
        if (count($this->options['query']) > 0) {
            $parameters = array_merge($this->options['query'], $parameters);
        }
        $url = $path;
        if (!empty($parameters)) {
            $url .= '?' . urldecode(http_build_query($parameters, null, '&'));
        }
        return htmlentities($url . $this->buildFragment());
    }

}