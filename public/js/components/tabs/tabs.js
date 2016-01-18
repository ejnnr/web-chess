System.register(['angular2/core', '../../services/tab-list.service', '../../services/tab-content.service'], function(exports_1) {
    var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
        var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
        if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
        else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
        return c > 3 && r && Object.defineProperty(target, key, r), r;
    };
    var __metadata = (this && this.__metadata) || function (k, v) {
        if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
    };
    var core_1, tab_list_service_1, tab_content_service_1;
    var TabsComponent;
    return {
        setters:[
            function (core_1_1) {
                core_1 = core_1_1;
            },
            function (tab_list_service_1_1) {
                tab_list_service_1 = tab_list_service_1_1;
            },
            function (tab_content_service_1_1) {
                tab_content_service_1 = tab_content_service_1_1;
            }],
        execute: function() {
            TabsComponent = (function () {
                function TabsComponent(_tabListService, _tabContentService) {
                    this._tabListService = _tabListService;
                    this._tabContentService = _tabContentService;
                    this._counter = 0;
                }
                TabsComponent.prototype.closeTab = function (tab) {
                    this._tabListService.removeTab(tab);
                };
                TabsComponent.prototype.getTabs = function () {
                    return this._tabListService.getTabs();
                };
                TabsComponent.prototype.newTab = function () {
                    this._tabListService.addTab({
                        "name": "Tab " + ++this._counter,
                        "layout": "greeter"
                    });
                };
                TabsComponent.prototype.getContent = function (tab) {
                    return this._tabContentService.getContent(tab);
                };
                TabsComponent = __decorate([
                    core_1.Component({
                        selector: 'tabs',
                        templateUrl: 'js/components/tabs/tabs.html',
                        styleUrls: ['js/components/tabs/tabs.css']
                    }), 
                    __metadata('design:paramtypes', [tab_list_service_1.TabListService, tab_content_service_1.TabContentService])
                ], TabsComponent);
                return TabsComponent;
            })();
            exports_1("TabsComponent", TabsComponent);
        }
    }
});

//# sourceMappingURL=tabs.js.map
