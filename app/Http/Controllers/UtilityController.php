<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UtilityController extends Controller
{

    /**
     * Invoice
     */
    public function invoice()
    {
        $breadcrumbsItems = [
            [
                'name' => 'Invoice',
                'url' => '/invoice',
                'active' => true
            ],

        ];
        return view('utility.invoice', [
            'pageTitle' => 'Invoice',
            'breadcrumbItems' => $breadcrumbsItems,
        ]);
    }


    /**
     * Pricing
     */
    public function pricing()
    {
        $breadcrumbsItems = [
            [
                'name' => 'Pricing',
                'url' => '/pricing',
                'active' => true
            ],

        ];
        return view('utility.pricing', [
            'pageTitle' => 'Pricing',
            'breadcrumbItems' => $breadcrumbsItems,
        ]);
    }

    /**
     * Blog
     */
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
    public function blogDetails()
    {
        $breadcrumbsItems = [
            [
                'name' => 'Blog Details',
                'url' => '/blog-details',
                'active' => true
            ],

        ];
        return view('utility.blog-details', [
            'pageTitle' => 'Blog Details',
            'breadcrumbItems' => $breadcrumbsItems,
        ]);
    }



    /**
     * Blog Details
     */
    public function blank()
    {
        $breadcrumbsItems = [
            [
                'name' => 'Blank',
                'url' => '/blank',
                'active' => true
            ],

        ];
        return view('utility.blank', [
            'pageTitle' => 'Blank',
            'breadcrumbItems' => $breadcrumbsItems,
        ]);
    }



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




    /**
     * Coming soon
     */
   



    /**
     * Under maintenance
     */

}
