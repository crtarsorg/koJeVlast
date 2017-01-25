<?php

//temp allow origin za Mihajla
header("Access-Control-Allow-Origin: http:/kojenavlasti.rs");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

    // This is the path to initialize.php, your site's gateway to the rest of the UF codebase!  Make sure that it is correct!
    $init_path = "../userfrosting/initialize.php";

    // This if-block just checks that the path for initialize.php is correct.  Remove this once you know what you're doing.
    if (!file_exists($init_path)){
        echo "<h2>We can't seem to find our way to initialize.php!  Please check the require_once statement at the top of index.php, and make sure it contains the correct path to initialize.php.</h2><br>";
    }

    require_once($init_path);

    use UserFrosting as UF;
   
    // Front page
    $app->get('/', function () use ($app) {
        // This if-block detects if mod_rewrite is enabled.
        // This is just an anti-noob device, remove it if you know how to read the docs and/or breathe through your nose.
        if (isset($_SERVER['SERVER_TYPE']) && ($_SERVER['SERVER_TYPE'] == "Apache") && !isset($_SERVER['HTTP_MOD_REWRITE'])) {
            $app->render('errors/bad-config.twig');
            exit;
        }
    
        // Check that we can connect to the DB.  Again, you can remove this if you know what you're doing.
        if (!UF\Database::testConnection()){
            // In case the error is because someone is trying to reinstall with new db info while still logged in, log them out
            session_destroy();
            // TODO: log out from remember me as well.
            $controller = new UF\AccountController($app);
            return $controller->pageDatabaseError();
        }
    
        // Forward to installation if not complete
        // TODO: Is there any way to detect that installation was complete, but the DB is malfunctioning?
        if (!isset($app->site->install_status) || $app->site->install_status == "pending"){
            $app->redirect($app->urlFor('uri_install'));
        }
        
        // Forward to the user's landing page (if logged in), otherwise take them to the home page
        // This is probably where you, the developer, would start making changes if you need to change the default behavior.
        if ($app->user->isGuest()){
            $controller = new UF\AccountController($app);
            $controller->pageHome();
        // If this is the first the root user is logging in, take them to site settings
        } else if ($app->user->id == $app->config('user_id_master') && $app->site->install_status == "new"){
            $app->site->install_status = "complete";
            $app->site->store();
            $app->alerts->addMessage("success", "Congratulations, you've successfully logged in for the first time.  Please take a moment to customize your site settings.");
            $app->redirect($app->urlFor('uri_settings'));
        } else {
            $app->redirect($app->user->landing_page);
        }
    })->name('uri_home');

    /********** FEATURE PAGES **********/
    
    $app->get('/dashboard/?', function () use ($app) {
        // Access-controlled page
        if (!$app->user->checkAccess('uri_dashboard')){
            $app->notFound();
        }
        
        $app->render('dashboard.twig', []);
    });
    
    $app->get('/zerg/?', function () use ($app) {
        // Access-controlled page
        if (!$app->user->checkAccess('uri_zerg')){
            $app->notFound();
        }
        
        $app->render('users/zerg.twig');
    });
       
    /********** ACCOUNT MANAGEMENT INTERFACE **********/
    
    $app->get('/account/:action/?', function ($action) use ($app) {
        // Forward to installation if not complete
        if (!isset($app->site->install_status) || $app->site->install_status == "pending"){
            $app->redirect($app->urlFor('uri_install'));
        }
    
        $get = $app->request->get();
        
        $controller = new UF\AccountController($app);
    
        $twig = $app->view()->getEnvironment();
        $loader = $twig->getLoader();
          
        switch ($action) {
            case "login":               return $controller->pageLogin();
            case "logout":              return $controller->logout(true);
            case "register":            return $controller->pageRegister();
            case "resend-activation":   return $controller->pageResendActivation();
            case "forgot-password":     return $controller->pageForgotPassword();
            case "activate":            return $controller->activate();
            case "set-password":        return $controller->pageSetPassword(true);
            case "reset-password":      if (isset($get['confirm']) && $get['confirm'] == "true")
                                            return $controller->pageSetPassword(false);
                                        else
                                            return $controller->denyResetPassword();
            case "captcha":             return $controller->captcha();
            case "settings":            return $controller->pageAccountSettings();
            default:                    return $controller->page404();
        }
    });

    $app->post('/account/:action/?', function ($action) use ($app) {
        $controller = new UF\AccountController($app);
    
        switch ($action) {
            case "login":               return $controller->login();
            case "register":            return $controller->register();
            case "resend-activation":   return $controller->resendActivation();
            case "forgot-password":     return $controller->forgotPassword();
            case "set-password":        return $controller->setPassword(true);
            case "reset-password":      return $controller->setPassword(false);
            case "settings":            return $controller->accountSettings();
            default:                    $app->notFound();
        }
    });
    
    /********** USER MANAGEMENT INTERFACE **********/
    
    // List users
    $app->get('/users/?', function () use ($app) {
        $controller = new UF\UserController($app);
        return $controller->pageUsers();
    })->name('uri_users');

    // List users in a particular primary group
    $app->get('/users/:primary_group/?', function ($primary_group) use ($app) {
        $controller = new UF\UserController($app);
        return $controller->pageUsers($primary_group);
    });
    
    // User info form (update)
    $app->get('/forms/users/u/:user_id/?', function ($user_id) use ($app) {
        $controller = new UF\UserController($app);
        return $controller->formUserEdit($user_id);
    });

    // User edit password form
    $app->get('/forms/users/u/:user_id/password/?', function ($user_id) use ($app) {
        $controller = new UF\UserController($app);
        $get = $app->request->get();
        return $controller->formUserEditPassword($user_id);
    });
    
    // User creation form
    $app->get('/forms/users/?', function () use ($app) {
        $controller = new UF\UserController($app);
        return $controller->formUserCreate();
    });
    
    // User info page
    $app->get('/users/u/:user_id/?', function ($user_id) use ($app) {
        $controller = new UF\UserController($app);
        return $controller->pageUser($user_id);
    });

    // Create user
    $app->post('/users/?', function () use ($app) {
        $controller = new UF\UserController($app);
        return $controller->createUser();
    });
    
    // Update user info
    $app->post('/users/u/:user_id/?', function ($user_id) use ($app) {
        $controller = new UF\UserController($app);
        return $controller->updateUser($user_id);
    });
    
    // Delete user
    $app->post('/users/u/:user_id/delete/?', function ($user_id) use ($app) {
        $controller = new UF\UserController($app);
        return $controller->deleteUser($user_id);
    });
    
    /********** GROUP MANAGEMENT INTERFACE **********/
    
    // List groups
    $app->get('/groups/?', function () use ($app) {
        $controller = new UF\GroupController($app);
        return $controller->pageGroups();
    });
    
    // List auth rules for a group
    $app->get('/groups/g/:group_id/auth?', function ($group_id) use ($app) {
        $controller = new UF\GroupController($app);
        return $controller->pageGroupAuthorization($group_id);
    })->name('uri_authorization');
    
    // Group info form (update)
    $app->get('/forms/groups/g/:group_id/?', function ($group_id) use ($app) {
        $controller = new UF\GroupController($app);
        return $controller->formGroupEdit($group_id);
    });

    // Group creation form
    $app->get('/forms/groups/?', function () use ($app) {
        $controller = new UF\GroupController($app);
        return $controller->formGroupCreate();
    });
    
    // Create group
    $app->post('/groups/?', function () use ($app) {
        $controller = new UF\GroupController($app);
        return $controller->createGroup();
    });
    
    // Update group info
    $app->post('/groups/g/:group_id/?', function ($group_id) use ($app) {
        $controller = new UF\GroupController($app);
        return $controller->updateGroup($group_id);
    });

    // Delete group
    $app->post('/groups/g/:group_id/delete/?', function ($group_id) use ($app) {
        $controller = new UF\GroupController($app);
        return $controller->deleteGroup($group_id);
    });
    
    /********** GROUP AUTH RULES INTERFACE **********/
    
    // Group auth creation form
    $app->get('/forms/groups/g/:group_id/auth/?', function ($group_id) use ($app) {
        $controller = new UF\AuthController($app);
        return $controller->formAuthCreate($group_id, "group");
    });
    
    // Group auth update form
    $app->get('/forms/groups/auth/a/:rule_id/?', function ($rule_id) use ($app) {
        $controller = new UF\AuthController($app);
        $get = $app->request->get();
        return $controller->formAuthEdit($rule_id);
    });

    // Group auth create
    $app->post('/groups/g/:group_id/auth/?', function ($group_id) use ($app) {
        $controller = new UF\AuthController($app);
        return $controller->createAuthRule($group_id, "group");
    });

    // Group auth update
    $app->post('/groups/auth/a/:rule_id?', function ($rule_id) use ($app) {
        $controller = new UF\AuthController($app);
        return $controller->updateAuthRule($rule_id, "group");
    });
    
    // Group auth delete
    $app->post('/auth/a/:rule_id/delete/?', function ($rule_id) use ($app) {
        $controller = new UF\AuthController($app);
        $get = $app->request->get();
        return $controller->deleteAuthRule($rule_id);
    });
        
    /************ ADMIN TOOLS *************/
    
    $app->get('/config/settings/?', function () use ($app) {
        $controller = new UF\AdminController($app);
        return $controller->pageSiteSettings();
    })->name('uri_settings');
    
    $app->post('/config/settings/?', function () use ($app) {
        $controller = new UF\AdminController($app);
        return $controller->siteSettings();
    });
    
    // Build the minified, concatenated CSS and JS
    $app->get('/config/build', function() use ($app){
        // Access-controlled page
        if (!$app->user->checkAccess('uri_minify')){
            $app->notFound();
        }
        
        $app->schema->build(true);
        $app->alerts->addMessageTranslated("success", "MINIFICATION_SUCCESS");
        $app->redirect($app->urlFor('uri_settings'));
    });
    
    // Slim info page
    $app->get('/sliminfo/?', function () use ($app) {
        // Access-controlled page
        if (!$app->user->checkAccess('uri_slim_info')){
            $app->notFound();
        }
        echo "<pre>";
        print_r($app->environment());
        echo "</pre>";
    });

    // PHP info page
    $app->get('/phpinfo/?', function () use ($app) {
        // Access-controlled page
        if (!$app->user->checkAccess('uri_php_info')){
            $app->notFound();
        }
        echo "<pre>";
        print_r(phpinfo());
        echo "</pre>";
    });

    // Error log page
    $app->get('/errorlog/?', function () use ($app) {
        // Access-controlled page
        if (!$app->user->checkAccess('uri_error_log')){
            $app->notFound();
        }
        $log = UF\SiteSettings::getLog();
        echo "<pre>";
        echo implode("<br>",$log['messages']);
        echo "</pre>";
    });
       
    /************ INSTALLER *************/

    $app->get('/install/?', function () use ($app) {
        $controller = new UF\InstallController($app);
        if (isset($app->site->install_status)){
            // If tables have been created, move on to master account setup
            if ($app->site->install_status == "pending"){
                $app->redirect($app->site->uri['public'] . "/install/master");
            } else {
                // Everything is set up, so go to the home page!
                $app->redirect($app->urlFor('uri_home'));
            }
        } else {
            return $controller->pageSetupDB();
        }
    })->name('uri_install');
    
    $app->get('/install/master/?', function () use ($app) {
        $controller = new UF\InstallController($app);
        if (isset($app->site->install_status) && ($app->site->install_status == "pending")){
            return $controller->pageSetupMasterAccount();
        } else {
            $app->redirect($app->urlFor('uri_install'));
        }
    });

    $app->post('/install/:action/?', function ($action) use ($app) {
        $controller = new UF\InstallController($app);
        switch ($action) {
            case "master":            return $controller->setupMasterAccount();
            default:                  $app->notFound();
        }
    });
    
    /************ API *************/
    
    $app->get('/api/users/?', function () use ($app) {
        $controller = new UF\ApiController($app);
        $controller->listUsers();
    });
    
    
    /************ MISCELLANEOUS UTILITY ROUTES *************/
    
    // Generic confirmation dialog
    $app->get('/forms/confirm/?', function () use ($app) {
        $get = $app->request->get();
        
        // Load the request schema
        $requestSchema = new \Fortress\RequestSchema($app->config('schema.path') . "/forms/confirm-modal.json");
        
        // Get the alert message stream
        $ms = $app->alerts;
        
        // Remove csrf_token
        unset($get['csrf_token']);
        
        // Set up Fortress to process the request
        $rf = new \Fortress\HTTPRequestFortress($ms, $requestSchema, $get);
    
        // Sanitize
        $rf->sanitize();
    
        // Validate, and halt on validation errors.
        if (!$rf->validate()) {
            $app->halt(400);
        }
        
        $data = $rf->data();
        
        $app->render('components/common/confirm-modal.twig', $data);
    });
    
    // Alert stream
    $app->get('/alerts/?', function () use ($app) {
        $controller = new UF\BaseController($app);
        $controller->alerts();
    });
    
    // JS Config
    $app->get($app->config('uri')['js-relative'] . '/config.js', function () use ($app) {
        $controller = new UF\BaseController($app);
        $controller->configJS();
    });
    
    // Theme CSS
    $app->get($app->config('uri')['css-relative'] . '/theme.css', function () use ($app) {
        $controller = new UF\BaseController($app);
        $controller->themeCSS();
    });
    
    // Not found page (404)
    $app->notFound(function () use ($app) {
        if ($app->request->isGet()) {
            $controller = new UF\BaseController($app);
            $controller->page404();
        } else {
            $app->alerts->addMessageTranslated("danger", "SERVER_ERROR");
        }
    });





    ////////////////////////////////////////
    /************ VLAST  ROUTES *************/
    /// lista aktera
    $app->get('/akteri/?', function () use ($app) {

        if (!$app->user->checkAccess('uri_akteri')){
            $app->notFound();
        }
        $evt = new UF\xAkter([]);
        $evt->listaAktera($app);
    });

    //Single akter data - list
    $app->get('/akteri/edit/:aid', function ($aid) use ($app) {

        if (!$app->user->checkAccess('uri_akteri_edit')){
            $app->notFound();
        }
        $evt = new UF\xAkter([]);
        $evt->editAktera($app,$aid);
    });


    //Single akter data - POST
    $app->post('/akteri/edit/:aid', function ($aid) use ($app) {

        if (!$app->user->checkAccess('uri_akteri_edit')){
            $app->notFound();
        }

        $evt = new UF\xAkter([]);
        $evt->editAkteraPost($app,$aid);
    });

    //Novi akter - forma
    $app->get('/akteri/add', function () use ($app) {

        if (!$app->user->checkAccess('uri_akteri_edit')){
            $app->notFound();
        }
        $evt = new UF\xAkter([]);
        $evt->addAktera($app);
    });

    //Novi akter - POST
    $app->post('/akteri/add', function () use ($app) {

        if (!$app->user->checkAccess('uri_akteri_edit')){
            $app->notFound();
        }

        $evt = new UF\xAkter([]);
        $evt->addAkteraPost($app);
    });





    ////////////////////////////////////////
    /// lista Funkcija
    $app->get('/funkcije/?', function () use ($app) {

        if (!$app->user->checkAccess('uri_funk')){
            $app->notFound();
        }
        $evt = new UF\xFunk([]);
        $evt->listaAktera($app);
    });

    //Funkcija edit data - forma
    $app->get('/funkcije/edit/:fid', function ($fid) use ($app) {

        if (!$app->user->checkAccess('uri_funk_edit')){
            $app->notFound();
        }
        $evt = new UF\xFunk([]);
        $evt->editAktera($app,$fid);
    });


    //Funkcija edit data - POST
    $app->post('/funkcije/edit/:fid', function ($fid) use ($app) {

        if (!$app->user->checkAccess('uri_funk_edit')){
            $app->notFound();
        }

        $evt = new UF\xFunk([]);
        $evt->editAkteraPost($app,$fid);
    });

    //Nova funkcija - forma
    $app->get('/funkcije/add', function () use ($app) {

        if (!$app->user->checkAccess('uri_funk_edit')){
            $app->notFound();
        }
        $evt = new UF\xFunk([]);
        $evt->addFunk($app);
    });

    //Nova funkcija - POST
    $app->post('/funkcije/add', function () use ($app) {

        if (!$app->user->checkAccess('uri_funk_edit')){
            $app->notFound();
        }

        $evt = new UF\xFunk([]);
        $evt->addFunkPost($app);
    });






    ////////////////////////////////////////
    /// lista opstina
    $app->get('/opstine/?', function () use ($app) {

        if (!$app->user->checkAccess('uri_opstine')){
            $app->notFound();
        }
        $evt = new UF\xOpstine([]);
        $evt->listaOpstina($app);
    });

    //Funkcija edit data - forma
    $app->get('/opstine/edit/:oid', function ($oid) use ($app) {

        if (!$app->user->checkAccess('uri_opstine_edit')){
            $app->notFound();
        }
        $evt = new UF\xOpstine([]);
        $evt->editOpstina($app,$oid);
    });


    //Funkcija edit data - POST
    $app->post('/opstine/edit/:oid', function ($oid) use ($app) {

        if (!$app->user->checkAccess('uri_opstine_edit')){
            $app->notFound();
        }

        $evt = new UF\xOpstine([]);
        $evt->editOpstinaPost($app,$oid);
    });

    //Nova funkcija - forma
    $app->get('/opstine/add', function () use ($app) {

        if (!$app->user->checkAccess('uri_opstine_edit')){
            $app->notFound();
        }
        $evt = new UF\xOpstine([]);
        $evt->addOpstina($app);
    });

    //Nova funkcija - POST
    $app->post('/opstine/add', function () use ($app) {

        if (!$app->user->checkAccess('uri_opstine_edit')){
            $app->notFound();
        }

        $evt = new UF\xOpstine([]);
        $evt->addOpstinaPost($app);
    });

    //OPstine - dodaj dokument
    $app->post('/opstine/addDoc', function () use ($app) {

        if (!$app->user->checkAccess('uri_opstine_edit')){
            $app->notFound();
        }

        $evt = new UF\xOpstine([]);
        $evt->opstinaAddDocPost($app);
    });


    //OPstine - dodaj dokument - API
    $app->get('/opstine/getDocs/:docid', function ($docid) use ($app) {

        $evt = new UF\xOpstine([]);
        $evt->opstinaGetDocs($app,$docid);
    });



    ////////////////////////////////////////
    /// Stranke
    $app->get('/stranke/?', function () use ($app) {

        if (!$app->user->checkAccess('uri_stranke')){
            $app->notFound();
        }
        $evt = new UF\xStranke([]);
        $evt->listaStranki($app);
    });

    //Stranka edit data - forma
    $app->get('/stranke/edit/:sid', function ($sid) use ($app) {

        if (!$app->user->checkAccess('uri_stranke_edit')){
            $app->notFound();
        }
        $evt = new UF\xStranke([]);
        $evt->editStranka($app,$sid);
    });


    //Stranka edit data - POST
    $app->post('/stranke/edit/:sid', function ($sid) use ($app) {

        if (!$app->user->checkAccess('uri_stranke_edit')){
            $app->notFound();
        }

        $evt = new UF\xStranke([]);
        $evt->editStrankaPost($app,$sid);
    });

    //Nova stranka - forma
    $app->get('/stranke/add', function () use ($app) {

        if (!$app->user->checkAccess('uri_stranke_edit')){
            $app->notFound();
        }
        $evt = new UF\xStranke([]);
        $evt->addStranka($app);
    });

    //Nova stranka - POST
    $app->post('/stranke/add', function () use ($app) {

        if (!$app->user->checkAccess('uri_stranke_edit')){
            $app->notFound();
        }

        $evt = new UF\xStranke([]);
        $evt->addStrankaPost($app);
    });







    ////////////////////////////////////////
    /// Koalicije
    $app->get('/koalicije/?', function () use ($app) {

        if (!$app->user->checkAccess('uri_koalicije')){
            $app->notFound();
        }
        $evt = new UF\xKo([]);
        $evt->lista($app);
    });

    //Nova koalicija - forma
    $app->get('/koalicije/add', function () use ($app) {

        if (!$app->user->checkAccess('uri_koalicije_edit')){
            $app->notFound();
        }
        $evt = new UF\xKo([]);
        $evt->addKoalicija($app);
    });

    //Nova koalicija - forma
    $app->post('/koalicije/add', function () use ($app) {

        if (!$app->user->checkAccess('uri_koalicije_edit')){
            $app->notFound();
        }
        $evt = new UF\xKo([]);
        $evt->addKoalicijaPost($app);
    });


    //Koalicija edit data - forma
    $app->get('/koalicije/edit/:kid', function ($kid) use ($app) {

        if (!$app->user->checkAccess('uri_koalicije_edit')){
            $app->notFound();
        }
        $evt = new UF\xKo([]);
        $evt->editKoalicija($app,$kid);
    });

    //Koalicija edit data - forma
    $app->post('/koalicije/edit/:kid', function ($kid) use ($app) {

        if (!$app->user->checkAccess('uri_koalicije_edit')){
            $app->notFound();
        }
        $evt = new UF\xKo([]);
        $evt->editKoalicijaPost($app,$kid);
    });

    //Lista stranaka
    $app->get('/koalicije/stranke', function () use ($app) {

        if (!$app->user->checkAccess('uri_koalicije_edit')){
            $app->notFound();
        }
        $evt = new UF\xKo([]);
        $evt->listaStranaka($app);
    });

    //Lista stranaka u koaliciji
    $app->get('/koalicije/stranke/:kid', function ($kid) use ($app) {

        if (!$app->user->checkAccess('uri_koalicije_edit')){
            $app->notFound();
        }
        $evt = new UF\xKo([]);
        $evt->listaStranakaUKoaliciji($app,$kid);
    });

    //Ukloni stranku iz koalicije
    $app->get('/koalicije/stranke/remove/:ksid', function ($ksid) use ($app) {

        if (!$app->user->checkAccess('uri_koalicije_edit')){
            $app->notFound();
        }
        $evt = new UF\xKo([]);
        $evt->ukloniStrankiIzKoalicije($app,$ksid);
    });

    //Dodaj stranku u koaliciju
    $app->get('/koalicije/stranke/add/:kid/:sid', function ($kid, $sid) use ($app) {

        if (!$app->user->checkAccess('uri_koalicije_edit')){
            $app->notFound();
        }
        $evt = new UF\xKo([]);
        $evt->dodajStrankuUKoaliciju($app,$kid,$sid);
    });





    ////////////////////////////////////////
    /// Promene
    $app->get('/promene/?', function () use ($app) {

        if (!$app->user->checkAccess('uri_promene')){
            $app->notFound();
        }
        $evt = new UF\xPromene([]);
        $evt->lista($app);
    });

    //Promene edit data - forma
    $app->get('/promene/edit/:pid', function ($pid) use ($app) {

        if (!$app->user->checkAccess('uri_promene_edit')){
            $app->notFound();
        }
        $evt = new UF\xPromene([]);
        $evt->editPromene($app,$pid);
    });


    //Promene edit data - POST
    $app->post('/promene/edit/:pid', function ($pid) use ($app) {

        if (!$app->user->checkAccess('uri_promene_edit')){
            $app->notFound();
        }

        $evt = new UF\xPromene([]);
        $evt->editPromenePost($app,$pid);
    });

    //Nova promena - forma
    $app->get('/promene/add', function () use ($app) {

        if (!$app->user->checkAccess('uri_promene_edit')){
            $app->notFound();
        }
        $evt = new UF\xPromene([]);
        $evt->addPromena($app);
    });

    //Nova promena - forma
    $app->post('/promene/add', function () use ($app) {

        if (!$app->user->checkAccess('uri_promene_edit')){
            $app->notFound();
        }
        $evt = new UF\xPromene([]);
        $evt->addPromenaPost($app);
    });


    $app->get('/err', function () use ($app) {

        if (!$app->user->checkAccess('uri_admin')){
            $app->notFound();
        }

        echo "Error log:
        <pre>";
        echo file_get_contents(ini_get('error_log'));
        echo "</pre>";


    });


    ////////////////////////////////////////
    /// Promene - ZAHTEV
    $app->get('/promeneZahtev/?', function () use ($app) {

        if (!$app->user->checkAccess('uri_promene_zahtev')){
            $app->notFound();
        }
        $evt = new UF\xPromeneZahtev([]);
        $evt->lista($app);
    });

    $app->post('/promeneZahtev/update', function () use ($app) {

        if (!$app->user->checkAccess('uri_promene_zahtev')){
            $app->notFound();
        }
        $evt = new UF\xPromeneZahtev([]);
        $evt->updateStatusZahteva($app);
    });





    ////////////////////////////////////////
    ////////////////////////////////////////
    ////////////////////////////////////////
    /// API
    ////////////////////////////////////////


    $app->get('/api/akter/promene/:pid', function ($pid) use ($app) {

        $evt = new UF\xApi([]);
        $evt->akterPromene($app,$pid);
    });

    $app->get('/api/opstine', function () use ($app) {

        $evt = new UF\xApi([]);
        $evt->listaOpstina($app);
    });

    $app->get('/api/stranke', function () use ($app) {

        $evt = new UF\xApi([]);
        $evt->listaStranaka($app);
    });

    //lista stranke na vlasti u trazenoj opstini
    $app->get('/api/strankaNaVlasti/:id', function ($id) use ($app) {

        $evt = new UF\xApi([]);
        $evt->strankaNaVlastiUOpstini($app,$id);
    });


    //lista sve opstine sa strankama na vlasti
    $app->get('/api/strankaNaVlasti', function () use ($app) {

        $evt = new UF\xApi([]);
        $evt->strankeNaVlastiPoOpstinama($app);
    });

    $app->get('/api/predlozitePromenu', function () use ($app) {

        $evt = new UF\xApi([]);
        $evt->predlozitePromenu($app);
    });

    $app->post('/api/predlozitePromenu', function () use ($app) {

        $evt = new UF\xApi([]);
        $evt->predlozitePromenuPost($app);
    });

    //lista aktera u trazenoj opstini
    $app->get('/api/akteriPoOpstini/:id', function ($id) use ($app) {

        $evt = new UF\xApi([]);
        $evt->akteriPoOpstini($app,$id);
    });



    $app->run();
