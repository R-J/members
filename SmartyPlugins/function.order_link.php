<?php
/**
 * Returns a get request for an ordered member list
 *
 * @param array $params The parameters passed into the function.
 *  - <b>field</b>: The field to order the list by.
 *  - <b>text</b>: The text to show in the link.
 *  - <b>class</b>: CSS class of the link.
 * @param Smarty $smarty The smarty object rendering the template.
 * @return A http query.
 */
function smarty_function_order_link($params, &$smarty) {
    $get = Gdn::request()->get();
    $get['order'] = val('field', $params, '');
    return anchor(
        val('text', $params, $get['order']),
        Gdn::request()->path().'?'.http_build_query($get),
        val('class', $params, '')
    );
}
