<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UtilityController extends Controller
{

    
    public function blog()
    {
        $breadcrumbsItems = [
            [
                'name' => 'Blog',
                'url' => '/blog',
                'active' => true
            ],

        ];
        return view('utility.blog', [
            'pageTitle' => 'Blog',
            'breadcrumbItems' => $breadcrumbsItems,
        ]);
    }

    /**
     * Blog Details
     */
   



    /**
     * Blog Details
     */
   


    /**
     * Profile
     */

    /**
     * error404
     */
    public function error404()
    {
        $breadcrumbsItems = [
            [
                'name' => 'error404',
                'url' => '/utility-404',
                'active' => true
            ],

        ];
        return view('utility.404', [
            'pageTitle' => 'Error 404',
            'breadcrumbItems' => $breadcrumbsItems,
        ]);
    }





}
