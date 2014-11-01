"use strict";

angular.module("eits-hover", []).directive('ngHover', [ function() {
	var HOVER_CLASS = "ng-hovered";
	return {
		restrict : 'A',
		require : 'ngModel',
		link : function(scope, element, attrs, ctrl) {
			ctrl.$hovered = false;

            if ( element.is('select') ){

                element.on('change', function(evt) {
                    element.parent().next().hide();
                }).on('mouseover', function(evt) {
                    if ( ctrl.$error.required ){
                        element.parent().next().show();
                    }
                }).on('mouseleave', function(evt) {
                    element.parent().next().hide();
                });
            }
            else if( element.is('div') ){
                element.on('mouseover', function(evt) {

                    element.parent().find("input, select").addClass(HOVER_CLASS);
                    element.parent().find("input, select").mouseover();
                    scope.$apply(function() {
                        ctrl.$hovered = true;
                    });

                }).on('mouseleave', function(evt) {

                    element.parent().find("input, select").removeClass(HOVER_CLASS);
                    element.parent().find("input, select").mouseleave();
                    scope.$apply(function() {
                        ctrl.$hovered = false;
                    });

                });
            }
            else{
                element.on('mouseover', function(evt) {

                    element.addClass(HOVER_CLASS);
                    scope.$apply(function() {
                        ctrl.$hovered = true;
                    });

                }).on('mouseleave', function(evt) {

                    element.removeClass(HOVER_CLASS);
                    scope.$apply(function() {
                        ctrl.$hovered = false;
                    });

                });
            }
		}
	};
} ]);