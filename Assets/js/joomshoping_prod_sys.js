window.Joomshoping_prod_sys = function () {
    var $ = jQuery ;
    var self = this;
    this.__group = 'system';
    this.__plugin = 'joomshoping_prod_sys' ;
    this.selectors = {
        btnBackupList : '#btn-a-backup-list',
        Fancybox : {
            baseClass : 'Backup-list-modal' ,
            btnRestore : '#toolbar-backup-restore button',
        }
    };
    this.AjaxDefaultData = {
        group : this.__group,
        plugin : this.__plugin ,
        option : 'com_jshopping' ,
        format : 'json' ,
        task : null ,
    }
    this.Init = function () {
        $(this.selectors.btnBackupList).on('click' , self.getListBackup )
    }
    this.getListBackup= function (event) {
        event.preventDefault();
        var data = self.AjaxDefaultData;
        data.task = 'getListBackup' ;
        self.getModul("Ajax").then(function (Ajax) {
            Ajax.send(data).then(function (r) {
                if (r.success !== true || typeof r.data !== 'object'  ) {
                    console.log(r)
                    alert('Err : getListBackup');
                    // return ;
                }
                Promise.all([
                    self.load.css('/libraries/GNZ11/Core/Backup/assets/css/Core.Backup.Joomla.css') ,
                ]) .then(function () {
                    self.BuildModal(r.data.html) ;
                });
            },function(err) {
                console.error(err)
            })
        });
    }
    this.BuildModal = function (html) {
        self.__loadModul.Fancybox().then(function (a) {
            console.log( a )
            a.open( html ,{
                baseClass: self.selectors.Fancybox.baseClass ,
                touch : false ,
                beforeShow  : function (instance, current){ },
                afterShow   : function(instance, current)   { },
                afterClose  : function () { },
            });

        },function (error) { console.log(error); })
    };

};
(function () {
    var I = setInterval(function () {
        if (typeof GNZ11 !== 'function') return ;
        clearInterval(I);
        window.Joomshoping_prod_sys.prototype = new GNZ11();
        window.Joomshoping_prod_sysObj = new Joomshoping_prod_sys();
        window.Joomshoping_prod_sysObj.Init();
    },1000)
})()



























