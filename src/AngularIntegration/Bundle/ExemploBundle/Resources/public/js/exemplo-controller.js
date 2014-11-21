'use strict';

function ExemploController( $scope, $injector, $log, $state, ServiceFactory ) {
    /**
     * Injeta os métodos, atributos e seus estados herdados de AbstractController.
     * @see AbstractController
     */

    $injector.invoke(AbstractController, this, {$scope: $scope});

    $scope.$on('ngGridEventSorted', function(event, sort) {

        // compara os objetos para garantir que o evento seja executado somente uma vez q não entre em loop
        if ( !angular.equals(sort, $scope.gridOptions.sortInfo) ) {
            $scope.gridOptions.sortInfo = angular.copy(sort);

            $scope.currentPage.sort.direction = sort.directions[0].toUpperCase();
            $scope.currentPage.sort.property = sort.fields[0];

            //$scope.listSampleByFilters();
        }
    });

    $scope.ServiceFactory = ServiceFactory;
    /*-------------------------------------------------------------------
     * 		 				 	ATTRIBUTES
     *-------------------------------------------------------------------*/

    //STATES
    /**
     * Variável estática que representa
     * o estado de listagem de registros.
     */
    $scope.LIST_STATE = "exemplo.listar";
    /**
     * Variável estática que representa
     * o estado de detalhe de um registro.
     */
    $scope.DETAIL_STATE = "exemplo.detalhe";
    /**
     * Variável estática que representa
     * o estado para a criação de registros.
     */
    $scope.INSERT_STATE = "exemplo.criar";

    /**
     * Variável estática que representa
     * o estado para a edição de registros.
     */
    $scope.UPDATE_STATE = "exemplo.editar";

    /**
     * Variável estática que representa
     * o estado para a guia
     */
    $scope.GUIA_STATE = "exemplo.guia";

    /**
     * Variável que armazena o estado corrente da tela.
     * Esta variável deve SEMPRE estar de acordo com a URL
     * que estão no browser.
     */
    $scope.currentState = "Exemplo.criar";

    /**
     * Armazena a entitidade corrente para edição ou detalhe.
     */
    $scope.currentEntity;


    /**
     * Armazena a entitidade corrente para edição ou detalhe.
     */
    $scope.currentFilter;

    var GRID_ACTION_BUTTONS = '<div style="float: left" class="cell-centered">' +
        '<a ui-sref="exemplo.editar({id:row.entity.id})" title="Editar" class="btn btn-mini"><i class="fa fa-pencil"></i></a>'+
        '<a ng-click="changeToRemove(row.entity.id)" title="Excluir" class="btn btn-mini"><i class="fa fa-trash-o"></i></a>'+
        '</div>';

    /*
    * Grid
    * */
    $scope.gridOptions = {
        data: 'currentPage.page.content',
        multiSelect: false,
        useExternalSorting: true,
        beforeSelectionChange: function (row, event) {
            if ( $(event.target).is("a") || $(event.target).is("i") ) return false;
            $state.go($scope.DETAIL_STATE, {id:row.entity.id});
        },
        columnDefs: [
                        {displayName: 'id', field: 'id'},
                        {displayName: 'name', field: 'name'},
                        {displayName:'Ações', sortable:false, cellTemplate: GRID_ACTION_BUTTONS, width:'20%'}
        ]
    };

    /**
     * Variável que armazena o estado da paginação
     * para renderizar o pager e também para fazer as requisições das
     * novas páginas, contendo o estado do Sort incluído.
     *
     * @type PageRequest
     */
    $scope.currentPage = {
        page:{
            content: new Array(),
            page: 1
        }
    };
    /*-------------------------------------------------------------------
     * 		 				 	  NAVIGATIONS
     *-------------------------------------------------------------------*/
    /**
     * Método principal que faz o papel de front-controller da tela.
     * Ele é invocado toda vez que ocorre uma mudança de URL (@see $stateChangeSuccess),
     * quando isso ocorre, obtém o estado através do $state e chama o método inicial daquele estado.
     * Ex.: /list -> changeToList()
     *      /criar -> changeToInsert()
     *
     * Caso o estado não for encontrado, ele direciona para a listagem,
     * apesar que o front-controller do angular não deixa digitar uma URL inválida.
     */
    $scope.initialize = function( toState, toParams, fromState, fromParams ) {
        var state = $state.current.name;

        switch (state) {
            case $scope.LIST_STATE: {
                $scope.changeToList();
            }
                break;
            case $scope.DETAIL_STATE: {
                $scope.changeToDetail( $state.params.id );
            }
                break;
            case $scope.INSERT_STATE: {
                $scope.changeToInsert();
            }
                break;
            case $scope.UPDATE_STATE: {
                $scope.changeToUpdate( $state.params.id );
            }
                break;
            case $scope.GUIA_STATE: {
                $scope.changeToGuia();
            }
                break;
            default : {
                $state.go( $scope.LIST_STATE );
            }
        }
    };

    $scope.changeToList = function() {
        $log.info("changeToList");

        $scope.currentEntity = new Exemplo();
        $scope.currentPage = new PageRequest();

        $scope.ServiceFactory.call("ExemploService", "listByFilters", $scope.currentEntity, function(data){
            $scope.currentPage = data;
            $scope.currentState = $scope.LIST_STATE;
        },
        function(data){
            console.log(data);
        });
    };

    $scope.changeToInsert = function() {

        $log.info("changeToInsert");

        $scope.currentEntity = new Exemplo();

        $scope.currentState = $scope.INSERT_STATE;

    };

    $scope.changeToUpdate = function( id, state ) {
        $log.info("changeToUpdate", id);

        $scope.currentEntity = new Exemplo();
        $scope.currentEntity.id = id;

        $scope.ServiceFactory.call("ExemploService", "findById", $scope.currentEntity, function(data){

                $scope.currentEntity = data;
                $scope.currentState = $scope.UPDATE_STATE;

            },
            function(data){
                console.log(data);
            });
    };

    $scope.changeToDetail = function( id, state ) {
        $log.info("changeToDetail", id);

        $scope.currentEntity = new Exemplo();
        $scope.currentEntity.id = id;

        $scope.ServiceFactory.call("ExemploService", "findById", $scope.currentEntity, function(data){


                $scope.currentEntity = data;

                $scope.currentState = $scope.DETAIL_STATE;

            },
            function(data){
                console.log(data);
            });
    };

    $scope.changeToRemove = function( id ) {
        $log.info("changeToRemove", id);

        $scope.currentEntity = new Exemplo();
        $scope.currentEntity.id = id;

        $scope.ServiceFactory.call("ExemploService", "remove", $scope.currentEntity, function(data){

                $scope.changeToList();
            },
            function(data){
                console.log(data);
            });
    };

    $scope.changeToGuia = function() {

        $log.info("changeToGuia");

        $scope.currentState = $scope.GUIA_STATE;

    };


    $scope.save = function(type){
        $scope.ServiceFactory.call("ExemploService", type, $scope.currentEntity, function(data){

            console.log(data);

                alert("Criado com sucesso!");
                $state.go( $scope.LIST_STATE );
            },
            function(data){
                console.log(data);
                alert("ops... deu algum erro!")
            });
    }




};