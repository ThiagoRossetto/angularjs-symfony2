"use strict";

angular.module("eits-grid-resize",[]).directive('resizeGridColumns', ['$window','$compile', function($window,$compile) {
    function resizer(scope, element, attrs) {
        var w = angular.element($window);

        scope.getWidth = function () {
            return w.width();
        };

        scope.$watch(scope.getWidth, function (newValue, oldValue) {
            if (typeof scope.resizeGridColumnsWidth == 'undefined')
                scope.resizeGridColumnsWidth = 767;

            if(newValue < scope.resizeGridColumnsWidth) {
                scope.resizeGridColumns = scope.resizeGridColumnsSet.smallColumns;
            }
            else {
                scope.resizeGridColumns = scope.resizeGridColumnsSet.allColumns;
            }
        }, false);

        w.bind('resize', function () {
            scope.$apply();
        });
    }

    return {
        restrict: 'A',
        scope: {
            resizeGridColumns: '=',
            resizeGridColumnsSet: '=',
            resizeGridColumnsWidth: '@'
        },
        link: resizer
    };

}]);