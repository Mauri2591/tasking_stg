<?php

use HTMLPurifier as GlobalHTMLPurifier;

class HtmlPurifier
{
    public static function purificar_html($data)
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,b,strong,i,u,ul,ol,li,br,span,font,div,blockquote,pre,h1,h2,h3,h4,h5,h6');
        $purifier = new HTMLPurifier($config);
        return $contenido_limpio = $purifier->purify($data);
    }
}
