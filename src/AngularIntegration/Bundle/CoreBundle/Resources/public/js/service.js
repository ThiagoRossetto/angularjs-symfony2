myapp
    .factory('ServiceFactory', ['$http', function ($http) {
        return {
            call: function (service, method, params, success, error)
            {
            	var data = {
            		'serviceName' : service,
            		'methodName' : method
            	};

                if(params){
                    data['parameters'] = [params];
                }else{
                    data['parameters'] = undefined;
                }

                $http({ 
                	method: 'POST', 
                	url:"app/broker", 
                	data: data,
                	transformResponse: function(data, headers){
                		var explicitRefact = function(obj){
                			for (var x in obj) {
                				if(x == "_explicitType"){
                					obj[x] = obj[x].split('\\').pop();
	                			}

							    if(typeof obj[x] === 'object'){
							    	explicitRefact(obj[x]);
							    }
							}
							return obj;
                		}
                        //Verifica se o retorno é um JSON

                        if (/^[\],:{}\s]*$/.test(data.replace(/\\["\\\/bfnrtu]/g, '@').
                        replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
                        replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
                            if(data == ""){
                                return true;    
                            }else{
                                return explicitRefact($.parseJSON(data));    
                            }
                        }else{
                            //TODO: Mensagem amigavel quando occorer um erro
                            return false;

                        }
                		
                	}
            	}).success(function(data){
                    if(data){
                        if(!data.error){
                            success(data);
                        }else{
                            error(data);
                        }
                    }else{
                        success(data);
                    }
                }).error(error);
            },
            callSync: function (service, method, params, success, error)
            {
                var data = {
                    'serviceName' : service,
                    'methodName' : method
                };

                if(params){
                    data['parameters'] = [params];
                }else{
                    data['parameters'] = undefined;
                }

                var ajax = $.ajax({
                    method: 'POST',
                    url:"app/broker",
                    data: JSON.stringify(data),
                    async: false,
                    contentType: 'application/json;charset=UTF-8',
                    dataType: 'json',
                    processData: false
                }).error(error);

                var explicitRefact = function(obj){
                    for (var x in obj) {
                        if(x == "_explicitType"){
                            obj[x] = obj[x].split('\\').pop();
                        }

                        if(typeof obj[x] === 'object'){
                            explicitRefact(obj[x]);
                        }
                    }
                    return obj;
                }
                //Verifica se o retorno é um JSON

                if (/^[\],:{}\s]*$/.test(ajax.responseText.replace(/\\["\\\/bfnrtu]/g, '@').
                    replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
                    replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
                    if(ajax.responseText == ""){
                        return true;
                    }else{
                        return explicitRefact($.parseJSON(ajax.responseText));
                    }
                }else{
                    //TODO: Mensagem amigavel quando occorer um erro
                    return false;

                }
            }
        };
} ]);