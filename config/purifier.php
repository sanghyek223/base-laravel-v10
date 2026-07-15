<?php

/**
 * HTMLPurifier configuration for Laravel.
 * This configuration allows most HTML tags and CSS styles, excluding scripts for security.
 */

return [
    'encoding'         => 'UTF-8',
    'finalize'        => true,
    'ignoreNonStrings' => false,
    'cachePath'       => storage_path('app/purifier'),
    'cacheFileMode'   => 0755,
    'settings'        => [
        'default' => [
            'HTML.Doctype'             => 'HTML 4.01 Transitional',
            'HTML.Allowed'             => 'div[style|class|id],b,strong,table[style|border|cellpadding|cellspacing|align|summary|bgcolor|width|height],thead,tbody,tfoot,tr[style|width|height|border],td[style|colspan|rowspan|width|height],th[style|colspan|rowspan|width|height],colgroup,col[style|width|span],i,em,u,a[href|title|target|rel|style|class|id],ul[style|class|id],ol[style|class|id],li[style|class|id],p[style|class|id],br,span[style|class|id],img[width|height|alt|src|style|title|class|id],h1[style|class|id],h2[style|class|id],h3[style|class|id],h4[style|class|id],h5[style|class|id],h6[style|class|id],blockquote[style|class|id],pre[style|class|id],code[style|class|id],figcaption[style|class|id],figure[style|class|id],footer[style|class|id],header[style|class|id],section[style|class|id],article[style|class|id],nav[style|class|id],aside[style|class|id],address[style|class|id],s,var,sub,sup,mark,wbr,ins[cite|datetime],del[cite|datetime],hr[style|class|id],iframe[src|width|height|frameborder|allowfullscreen|style|class|id],video[src|width|height|controls|style|class|id],audio[src|controls|style|class|id],source[src|type],track[src|kind|srclang|label],canvas[width|height|style|class|id]',
            'HTML.AllowedAttributes'   => 'style,class,id,href,title,target,src,alt,width,height,border,cellpadding,cellspacing,colspan,rowspan,rel,type,controls,frameborder,allowfullscreen,cite,datetime,kind,srclang,label,span',
            'CSS.AllowedProperties'    => 'background,background-color,color,float,font,font-family,font-size,font-style,font-weight,height,line-height,margin,margin-bottom,margin-left,margin-right,margin-top,padding,padding-bottom,padding-left,padding-right,padding-top,text-align,text-decoration,vertical-align,width,max-width,border,border-collapse,border-spacing,letter-spacing,display,border-top,border-bottom,border-left,border-right,border-color,border-style,border-width',
            'HTML.ForbiddenElements'   => 'script',
            'CSS.AllowImportant'       => true,
            'CSS.AllowTricky'          => true,
            'HTML.SafeIframe'          => true,
            'URI.SafeIframeRegexp'     => '%^(http://|https://|//)(www.youtube.com/embed/|player.vimeo.com/video/|maps.google.com/|www.google.com/maps/embed|player.twitch.tv/|www.facebook.com/plugins/|platform.twitter.com/|www.instagram.com/|www.tiktok.com/embed)%',
            'AutoFormat.AutoParagraph' => false,
            'AutoFormat.RemoveEmpty'   => false,
            'Core.EscapeInvalidTags'   => true,
            'HTML.MaxImgLength'        => null,
            'CSS.MaxImgLength'         => null,
        ],
        'custom_definition' => [
            'id'       => 'html5-definitions',
            'rev'      => 1,
            'debug'    => false,
            'elements' => [
                ['section', 'Block', 'Flow', 'Common'],
                ['nav', 'Block', 'Flow', 'Common'],
                ['article', 'Block', 'Flow', 'Common'],
                ['aside', 'Block', 'Flow', 'Common'],
                ['header', 'Block', 'Flow', 'Common'],
                ['footer', 'Block', 'Flow', 'Common'],
                ['address', 'Block', 'Flow', 'Common'],
                ['figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common'],
                ['figcaption', 'Inline', 'Flow', 'Common'],
                ['video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', [
                    'src'      => 'URI',
                    'type'     => 'Text',
                    'width'    => 'Length',
                    'height'   => 'Length',
                    'poster'   => 'URI',
                    'preload'  => 'Enum#auto,metadata,none',
                    'controls' => 'Bool',
                ]],
                ['audio', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', [
                    'src'      => 'URI',
                    'type'     => 'Text',
                    'controls' => 'Bool',
                ]],
                ['source', 'Block', 'Flow', 'Common', [
                    'src'  => 'URI',
                    'type' => 'Text',
                ]],
                ['track', 'Block', 'Flow', 'Common', [
                    'src'     => 'URI',
                    'kind'    => 'Text',
                    'srclang' => 'Text',
                    'label'   => 'Text',
                ]],
                ['canvas', 'Block', 'Flow', 'Common', [
                    'width'  => 'Length',
                    'height' => 'Length',
                ]],
                ['s', 'Inline', 'Inline', 'Common'],
                ['var', 'Inline', 'Inline', 'Common'],
                ['sub', 'Inline', 'Inline', 'Common'],
                ['sup', 'Inline', 'Inline', 'Common'],
                ['mark', 'Inline', 'Inline', 'Common'],
                ['wbr', 'Inline', 'Empty', 'Core'],
                ['ins', 'Block', 'Flow', 'Common', ['cite' => 'URI', 'datetime' => 'CDATA']],
                ['del', 'Block', 'Flow', 'Common', ['cite' => 'URI', 'datetime' => 'CDATA']],
            ],
            'attributes' => [
                ['iframe', 'allowfullscreen', 'Bool'],
                ['table', 'height', 'Text'],
                ['td', 'border', 'Text'],
                ['th', 'border', 'Text'],
                ['tr', 'width', 'Text'],
                ['tr', 'height', 'Text'],
                ['tr', 'border', 'Text'],
            ],
        ],
        'custom_attributes' => [
            ['a', 'target', 'Enum#_blank,_self,_target,_top'],
        ],
        'custom_elements' => [
            ['u', 'Inline', 'Inline', 'Common'],
        ],
    ],
];
