@extends('themes::layout')

@php
    $menu = \Ophim\Core\Models\Menu::getTree();
@endphp

@push('header')

<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500&amp;display=swap' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="{{ asset('/build/bootstrap/css/bootstrap.min.css') }}" as="style" rel="preload" />
<link rel="stylesheet" type="text/css" href="{{ asset('/build/css/can-toggle.css?v=1.0') }}" as="style" rel="preload">
<link rel="stylesheet" type="text/css" href="{{ asset('/build/css/font-awesome.min.css') }}" rel="preload" as="font" />
<link rel="stylesheet" type="text/css" href="{{ asset('/build/css/mainchill.css') }}" as="style" rel="preload" />
<link rel="stylesheet" type="text/css" href="{{ asset('/build/css/responsive.css') }}" as="style" rel="preload" />
<link rel="stylesheet" type="text/css" href="{{ asset('/build/css/page_index.css') }}" as="style" rel="preload" />
<script type="text/javascript" src="{{ asset('/build/js/jquery-1.11.1.min.js') }}"></script>
<script defer type="text/javascript" src="{{ asset('/build/bootstrap/js/bootstrap.min.js') }}"></script>
<script defer type="text/javascript" src="{{ asset('/build/js/lazyz.min.js') }}"></script>
<script defer type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script defer type="text/javascript" src="{{ asset('/build/js/functions.js') }}"></script>
<script defer type="text/javascript" src="{{ asset('/build/js/action.js') }}"></script>

<link rel='dns-prefetch' href='http://fonts.googleapis.com/' />
<link href='https://fonts.gstatic.com/' crossorigin rel='preconnect' />
    
{!! setting('site_scripts_google_analytics') !!}
@endpush

@section('body')
    @include('themes::themepmc.inc.header')
    <div id="main-content">
        @if (get_theme_option('ads_header'))
            {!! get_theme_option('ads_header') !!}
        @endif
        <div id="content">
            @yield('content')
        </div>
    </div>
    {!! get_theme_option('tag_box') !!}
@endsection

@section('footer')
    {!! get_theme_option('footer') !!}

    @if (get_theme_option('ads_catfish'))
        {!! get_theme_option('ads_catfish') !!}
    @endif

    <script>
        var $menu = $("#menu-mobile");
        var $over_lay = $('#overlay_menu');
        var hw = $(window).height();

        function set_height_menu() {}

        function open_menu() {
            $('body').addClass('menu-active');
            $menu.addClass('expanded');
            set_height_menu();
            $(".btn-humber").addClass('active');
        }

        function close_menu() {
            $('body').removeClass('menu-active');
            $menu.removeClass('expanded');
            var w_scroll_top = $(window).scrollTop();
            if (w_scroll_top >= 50) {
                pos_top_menu = 0;
            } else {
                pos_top_menu = w_scroll_top;
            }
            set_height_menu();
            $(".btn-humber").removeClass('active');
        }
        $(document).ready(function() {
            $(".btn-humber").click(function() {
                if ($menu.hasClass('expanded')) {
                    close_menu();
                } else {
                    open_menu();
                }
            });
            $(window).scroll(function() {
                set_height_menu();
            });
            $(".parent-menu").click(function(e) {
                e.preventDefault();
                $this = $(this);
                $arrow = $this.find('.fa');
                if ($arrow.length && event.target.className != 'sub-menu-link') {
                    if ($arrow.hasClass('fa-angle-down')) {
                        $arrow.removeClass('fa-angle-down').addClass('fa-angle-up');
                    } else {
                        $arrow.addClass('fa-angle-down').removeClass('fa-angle-up');
                    }
                    $this.find('.sub-menu').toggle();
                    return false;
                } else {
                    var href = event.target.href;
                    window.location.href = href;
                }
            });
        });
    </script>
<!-- css Ads  -->
<style>
        .btn-xs,
        .btn-group-xs>.btn {
            font-size: 14px;
        }
        
        .float-ck {
            position: fixed;
            bottom: 0;
            z-index: 2099;
        }
        
        .float-top {
            position: fixed;
            top: 0;
            z-index: 2099;
        }
        
        .catfishs-ck {
            position: fixed;
            bottom: 0;
            z-index: 2099;
        }
        
        @media only screen and (max-width: 1025px) {
            .hidemobile {
                display: none !important;
            }
            #catfishs_content img {
                width: 80%;
                height: 100%;
            }
        }
        
        @media only screen and (min-width: 1026px) {
            .hidedesktop {
                display: none!important;
            }
        }
        
        .off a {
            background: #01AEF0;
            padding: 5px 10px;
            color: #FFF;
        }
        
        .top-block.right-box {
            width: 100%;
            margin-top: 10px
        }
        
        .right-box-header {
            color: orange;
            font-size: 15px;
            margin-bottom: 10px;
            display: inline-block;
            margin-top: 15px;
        }
        
        .box_odds {
            background: #000;
            position: relative;
            padding: 5px;
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px
        }
        
        .box_odds .text-1 {
            font-size: 12px
        }
        
        .box_odds .text-2 {
            font-weight: 700;
            font-size: 14px;
            line-height: 130%;
            color: #f9bc27
        }
        
        .box_odds .img img {
            width: 90px;
            padding: 5px;
            border-radius: 4px;
            background-color: #495262;
            display: flex
        }
        
        .odds-info {
            width: calc(100% - 115px);
            justify-content: space-between;
            align-self: center;
            color: #fff;
            padding: 5px 5px 5px 0;
            text-align: center
        }
        
        .odds-info a {
            line-height: 16px;
            display: inline-block;
            width: 40%;
            padding: 4px 0;
            border-radius: 4px;
            font-size: 13px;
            color: #1b273c;
            font-weight: 700;
            background-color: #f9bc27;
            border: 1px solid transparent;
            text-align: center;
            height: 25px;
            box-sizing: border-box
        }
        
        .odds-info a+a {
            margin-left: 5px
        }
        
        @media (max-width: 767px) {
            .top-block {
                margin-top: 10px;
                padding: 0 10px;
            }
            .right-box-header {
                margin-bottom: 0;
                font-size: 14px
            }
        }
</style>
<script defer>
        function an_catfish() {
            var content = document.getElementById('catfishs_content');
            var hide = document.getElementById('an_catfish');
            if (content.style.display == "none") {
                content.style.display = "block";
                hide.innerHTML = '<a rel="nofollow" href="javascript:an_catfish()">X</a>';
            } else {
                content.style.display = "none";
                hide.style.display = "none";
            }
        }
    </script>
@endsection
