System.register(['angular2/core'], function(exports_1) {
    var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
        var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
        if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
        else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
        return c > 3 && r && Object.defineProperty(target, key, r), r;
    };
    var __metadata = (this && this.__metadata) || function (k, v) {
        if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
    };
    var core_1;
    var TabListService;
    return {
        setters:[
            function (core_1_1) {
                core_1 = core_1_1;
            }],
        execute: function() {
            TabListService = (function () {
                function TabListService() {
                    this._tabs = [];
                }
                TabListService.prototype.getTabs = function () {
                    return this._tabs;
                };
                TabListService.prototype.addTab = function (tab) {
                    tab.id = this._tabs.length; // overwrite id to be the index of the tab in the array
                    this._tabs.push(tab);
                };
                TabListService.prototype.removeTab = function (tab) {
                    var index = this._tabs.indexOf(tab);
                    if (index > -1) {
                        this._tabs.splice(index, 1);
                    }
                };
                TabListService = __decorate([
                    core_1.Injectable(), 
                    __metadata('design:paramtypes', [])
                ], TabListService);
                return TabListService;
            })();
            exports_1("TabListService", TabListService);
        }
    }
});

//# sourceMappingURL=tab-list.service.js.map
