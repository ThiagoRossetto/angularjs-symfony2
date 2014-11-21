"use strict";


var myapp = angular.module("AngularIntegrationApp", ['ui.bootstrap', 'ngGrid', 'ui.router',  'ngSanitize', 'ui.date','localytics.directives', 'ui.sortable'])

    .config( function( $stateProvider , $urlRouterProvider ) {

        $urlRouterProvider.otherwise("/");

        //Feel free to change this state
        $stateProvider.state( 'root', {
            url         : '/',
            templateUrl : 'bundles/angularintegrationcore/templates/root.html'
        });

        /*
         * Important: Don't remove the @beginStates annotation
         * */

        //@beginStates

        $stateProvider.state('exemplo', {
                url : "/exemplo",
                templateUrl : "bundles/angularintegrationexemplo/templates/exemplo/exemplo-view.html",
                controller : ExemploController
            })
                .state('exemplo.detalhe', {
                    url: "/detalhe/:id"
                })
                .state('exemplo.listar', {
                    url: "/listar"
                })
                .state('exemplo.criar', {
                    url : "/criar"
                })
                .state('exemplo.editar', {
                    url : "/editar/:id"
                })
                .state('exemplo.guia', {
                    url : "/guia"
                });

       /* $stateProvider.state('guia', {
            url : "/guia",
            templateUrl : "bundles/angularintegrationexemplo/templates/guia/guia-view.html",
            controller : ExemploController
        })
            .state('guia.instalar', {
                url: "/detalhe/:id"
            })
            .state('guia.utilizar', {
                url: "/listar"
            });
*/

    });