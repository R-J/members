<?php
/**
 * Returns a user photo.
 *
 * @param array $params The parameters passed into the function.
 *  - <b>user</b>: The user.
 *  - <b>options</b>: Options for the users photo.
 * @param Smarty $smarty The smarty object rendering the template.
 * @return The users photo.
 */
function smarty_function_user_photo($params, &$smarty) {
    return userPhoto(
        val('user', $params, ''),
        val('options', $params, ['Size' => 'Small'])
    );
}
