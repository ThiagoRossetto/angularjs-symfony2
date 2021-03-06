'use strict';

/**
 *
 * @param $scope
 * @param $log
 * @param $filter
 */
function AbstractController( $scope, $log, $filter) {


    /*-------------------------------------------------------------------
     * 		 				 	ATTRIBUTES
     *-------------------------------------------------------------------*/
    $scope.INVALID_FORM_MESSAGE = "_[[Os campos em destaque são de preenchimento obrigatório.]]";
    $scope.INVALID_ID_MESSAGE = "_[[Não foi possível abrir o detalhe do registro. O identificador é inválido.]]";
    $scope.SERVER_ERROR = "_[[Ocorreu um problema. Por favor contate seu administrador.]]";


   /* $scope.API = API;*/
    /*-------------------------------------------------------------------
     * 		 				 	EVENT HANDLERS
     *-------------------------------------------------------------------*/
    /**
     * Handler que escuta as mudan�as de URLs pertecentes ao estado da tela.
     * Ex.: litar, criar, detalhe, editar
     *
     * Toda vez que ocorre uma mudan�a de URL se via bot�o, troca de URL manual, ou ainda
     * ao van�ar e voltar do browser, este evento � chamado e chama o initilize() que faz o papel de front-controller.
     *
     */
    $scope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams) {
        $log.info("$stateChangeSuccess");

        $scope.initialize(toState, toParams, fromState, fromParams);

    });

    /**
     * Retorna a instancia do form no escopo do angular.
     * @param formName
     * @returns {*|Function|$scope.form|$scope.form|$scope.form|jQuery}
     */
    $scope.form = function( formName ) {
        return $("form[name="+(formName ? formName: 'form')+"]").scope()[(formName ? formName: 'form')];
    };

}