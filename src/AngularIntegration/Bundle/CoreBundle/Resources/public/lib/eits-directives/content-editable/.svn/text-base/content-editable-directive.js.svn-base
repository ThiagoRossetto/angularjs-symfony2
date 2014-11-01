"use strict";

angular.module('eits-editable', [])
    .directive('contentEditable', function($parse) {
        return {
            require: 'ngModel',
            link: function(scope, elm, attrs, ctrl) {
                // view -> model
                var editable = elm[0],selection, range;
                var eventa, html, div;

                var captureSelection = function(e) {

                    // Don't capture selection outside editable region
                    var isOrContainsAnchor = false,
                        isOrContainsFocus = false,
                        sel = window.getSelection(),
                        parentAnchor = sel.anchorNode,
                        parentFocus = sel.focusNode;

                    while(parentAnchor && parentAnchor != document.documentElement) {
                        if(parentAnchor == editable) {
                            isOrContainsAnchor = true;
                        }
                        parentAnchor = parentAnchor.parentNode;
                    }

                    while(parentFocus && parentFocus != document.documentElement) {
                        if(parentFocus == editable) {
                            isOrContainsFocus = true;
                        }
                        parentFocus = parentFocus.parentNode;
                    }

                    if(!isOrContainsAnchor || !isOrContainsFocus) {
                        return;
                    }

                    selection = window.getSelection();

                    // Get range (standards)
                    if(selection.getRangeAt !== undefined) {
                        range = selection.getRangeAt(0);

                        // Get range (Safari 2)
                    } else if(
                        document.createRange &&
                            selection.anchorNode &&
                            selection.anchorOffset &&
                            selection.focusNode &&
                            selection.focusOffset
                        ) {
                        range = document.createRange();
                        range.setStart(selection.anchorNode, selection.anchorOffset);
                        range.setEnd(selection.focusNode, selection.focusOffset);
                    } else {
                        // Failure here, not handled by the rest of the script.
                        // Probably IE or some older browser
                    }
                };

                elm.bind('blur', function() {
                    scope.$apply(function() {
                        ctrl.$setViewValue(elm.html());
                    });
                });

                elm.bind('keydown', function(event) {
                    if(attrs.readonly)
                        return false;

                    if(event.keyCode == 13)
                        return false;

                    captureSelection(event);
                    eventa = event;
                    html = $(this).html();
                    div = $(this);

                    setTimeout(function(){
                        /*if($(editable).text().length > attrs.maxlength){
                            $(editable).text($(editable).text().substr(0,attrs.maxlength));
                            placeCaretAtEnd(editable);
                        }*/

                        var sel = window.getSelection();
                        if(eventa.keyCode == 8 || eventa.keyCode == 46){
                            var end = $(range.endContainer.parentElement);
                            var start = $(range.startContainer.parentElement);
                            if( end.is( "a" ) ){
                                end.remove("a");
                            }
                            if( start.is( "a" ) ){
                                start.remove("a");
                            }
                            if($(sel.anchorNode.parentElement).is("a")){
                                $(sel.anchorNode.parentElement).remove("a");
                            }
                        }

                    },1);

                });

                // model -> view
                ctrl.$render = function() {
                    elm.html(ctrl.$viewValue);
                };
            }
        }
    });


function placeCaretAtEnd(el) {
    el.focus();
    if (typeof window.getSelection != "undefined"
        && typeof document.createRange != "undefined") {
        var range = document.createRange();
        range.selectNodeContents(el);
        range.collapse(false);
        var sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
    } else if (typeof document.body.createTextRange != "undefined") {
        var textRange = document.body.createTextRange();
        textRange.moveToElementText(el);
        textRange.collapse(false);
        textRange.select();
    }
}
