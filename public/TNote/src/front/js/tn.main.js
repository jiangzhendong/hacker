Jx().$package('tn.main', function(J){
    
    var packageContext = this;

    this.init = function(){
        tn.view.init();
        //... any other logic
        //
    
        //main logic
        J.event.notifyObservers(packageContext, 'systemReady');
    }

});