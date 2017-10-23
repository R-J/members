<?php

$PluginInfo['members'] = [
    'Name' => 'Members',
    'Description' => 'UNFINISHED: Shows a list of all forum members.',
    'Version' => '0.0.1',
    'RequiredApplications' => ['Vanilla' => '>=2.3'],
    'MobileFriendly' => true,
    'HasLocale' => true,
    'SettingsUrl' => 'settings/members',
    'RegisterPermissions' => ['Plugins.Members.View'],
    'Author' => 'Robin Jurinka',
    'AuthorUrl' => 'https://open.vanillaforums.com/profile/r_j'
];

/**
 * Adds a member list to Vanilla.
 *
 * Provide several custom views and give a setting page where views can
 * be assigned to roles
 * Admin can define which info from User table and UserMeta table should be
 * viewable per role.
 */
class MembersPlugin extends Gdn_Plugin {
    public function setup() {
        touchConfig('members.PerPage', 30);
        $this->structure();
    }

    /**
     * Todo: add UserRole (not private information)
     * @return [type] [description]
     */
    public function structure() {
        // Create View which holds user information.
        $userTableSchema = Gdn::sql()->fetchTableSchema('User');
        unset($userTableSchema['Password']);
        unset($userTableSchema['HashMethod']);
        unset($userTableSchema['Attributes']);
        unset($userTableSchema['Preferences']);
        unset($userTableSchema['Permissions']);

        $userTableFields = array_keys($userTableSchema);

        $sql = Gdn::sql()->from('User u');
        foreach ($userTableFields as $field) {
            $sql->select('u.'.$field);
        }
        $profileExtenderFields = array_keys(c('ProfileExtender.Fields', []));
        foreach ($profileExtenderFields as $index => $field) {
            $sql->select("um{$index}.Name", '', "Profile{$field}")
                ->join(
                    "UserMeta um{$index}",
                    "u.UserID = um{$index}.UserID and um{$index}.Name = 'Profile.{$field}'",
                    'left'
                );
        }
        $sql->where(['u.Deleted' => 0]);

        $query = $sql->applyParameters(
            $sql->getSelect(),
            $sql->namedParameters()
        );

        $sql->reset();

        Gdn::database()
            ->structure()
            ->view('VIEW_Members', $query);
    }


    public function onDisable() {
        // destroy view
    }

    /**
     * Add folder for custom smarty functions.
     *
     * @param Smarty $sender Instance of the Smarty templating engine.
     *
     * @return void.
     */
    public function gdn_smarty_init_handler($sender) {
        $sender->plugins_dir[] = dirname(__FILE__).DS.'SmartyPlugins';
    }

	/**
	 * Setting page.
     *
	 * Show a matrix to define which roles are allowed
     * to see which information.
     *
	 * @param settingsController $sender The calling object.
     *
	 * @return void.
	 */
	public function settingsController_members_create($sender) {
        // Check settings permissions.
        Gdn::session()->checkPermission('Garden.Settings.Manage');
        $sender->addSideMenu('dashboard/settings/plugins');
        $sender->setData('Title', t('Members List Settings'));
        $sender->setData('Description', t('Members.Description', 'Please choose a view for each role.'));
        $sender->setData('Warning', t('Members.Warning', 'This is no replacement for permissions! Permissions must be set extra. Each role with the correct permission and no assigned view will be presented with the default view.'));

        $roleModel = new RoleModel();
        $roles = $roleModel->get()->resultArray();
        $permittedRoles = $roleModel->getByPermission('Plugins.Members.View')->resultArray();
        $views = [];
        foreach (glob(__DIR__.'/views/*') as $view) {
            $filename = basename($view);
            if ($filename != 'settings.php') {
                $views[] = $filename;
            }
        }
        decho($roles);
        decho($permittedRoles);
        decho($views);


        return;
        // get roles
        // get role permissions
        // get available views
        // Show Role, dropdown with view, but give hint if role has permission


        $sender->render($sender->fetchViewLocation('settings', '', 'plugins/members'));






if ($sender->Form->authenticatedPostBack()) {
    decho($sender->Form->formValues());
}
        $sender->addSideMenu('dashboard/settings/plugins');
        $sender->setData('Title', t('Members List Settings'));
        $sender->setData('Description', t('Members.Description', 'Please choose which user role should see which information.'));
        $sender->setData('Warning', t('Members.Warning', 'This is a mighty tool and you have to take care not to expose private data!'));

    //    $this->setData(
        $userTableSchema = Gdn::sql()->fetchTableSchema('User');
decho(c('ProfileExtender.Fields', []));
        unset($userTableSchema['Password']);
        $userTableFields = array_keys($userTableSchema);
        $profileExtenderFields = array_keys(c('ProfileExtender.Fields', []));
        array_walk($profileExtenderFields, function(&$field) {
            $field = 'Profiles.'.$field;
        });

        decho($userTableFields);
        decho($profileExtenderFields);
        return;


        $sender->render($this->getView('settings.php'));
    }

    /**
     * Add custom menu entry to all pages.
     *
     * @param BaseController $sender Instance of the calling class.
     *
     * @return void.
     */
    public function base_render_before($sender) {
        if ($sender->Menu) {
            $sender->Menu->addLink(
                'Members',
                t('Members'),
                '/vanilla/members',
                ['Plugins.Members.View']
            );
        }
    }

    /**
     * Show member list depending on visitors role
     *
     * @param VanillaController $sender Instance of the calling class.
     * @param String $page Page number.
     *
     * @return void.
     */
    public function vanillaController_members_create($sender, $page = false) {
        $sender->permission('Plugins.Members.View');

        // Determine offset from $page
        list($offset, $limit) = offsetLimit($page, c('members.PerPage', 30), true);
        $page = pageNumber($offset, $limit);

        // Set canonical URL.
        $sender->canonicalUrl(url(concatSep(
            '/',
            'vanilla',
            'members',
            pageNumber($offset, $limit, true, false)
        ), true));

        $sender->title(t('Members'));

        // Add modules
        $sender->addModule('DiscussionFilterModule');
        $sender->addModule('NewDiscussionModule');
        $sender->addModule('CategoriesModule');
        $sender->addModule('BookmarkedModule');
        $sender->addModule('TagModule');

        $sender->setData('Breadcrumbs', [['Name' => t('Members'), 'Url' => '/vanilla/members']]);

        $countMembers = Gdn::sql()->getCount('VIEW_Members');
        $sender->setData('CountMembers', $countMembers);

        $sort = $sender->Request->get('sort', 'Name');
        $order = $sender->Request->get('order', 'asc');

        // Get Members.
        $sender->setData(
            'Members',
            Gdn::sql()
                ->get(
                    'VIEW_Members',
                    $sort,
                    $order,
                    $limit,
                    $page
                )
        );

/*
Get users by role: admin + mods
Map admin + mod info to users
 */


        // Build a pager
        $pagerFactory = new Gdn_PagerFactory();
        if (!$sender->data('PagerUrl')) {
            $sender->setData('PagerUrl', 'vanilla/members/{Page}');
        }
// $sender->setData('PagerUrl', $sender->data('PagerUrl'));
        $sender->Pager = $pagerFactory->getPager('Pager', $sender);
        $sender->Pager->ClientID = 'Pager';
        $sender->Pager->configure(
            $offset,
            $limit,
            $countMembers,
            $sender->data('PagerUrl')
        );
        PagerModule::current($sender->Pager);
        $sender->setData('Pager', $sender->Pager->toString('more'));

        $sender->View = 'default';

        $sender->render($sender->fetchViewLocation('default', '', 'plugins/members'));
        // $sender->render();
    }
}
