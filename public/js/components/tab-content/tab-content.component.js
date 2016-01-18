System.register(['angular2/core', '../../services/tab-content.service'], function(exports_1) {
    var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
        var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
        if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
        else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
        return c > 3 && r && Object.defineProperty(target, key, r), r;
    };
    var __metadata = (this && this.__metadata) || function (k, v) {
        if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
    };
    var core_1, tab_content_service_1;
    var TabContentComponent;
    return {
        setters:[
            function (core_1_1) {
                core_1 = core_1_1;
            },
            function (tab_content_service_1_1) {
                tab_content_service_1 = tab_content_service_1_1;
            }],
        execute: function() {
            TabContentComponent = (function () {
                function TabContentComponent(_tabContentService) {
                    this._tabContentService = _tabContentService;
                }
                TabContentComponent.prototype.getContent = function () {
                    return this._tabContentService.getContent(this.tab);
                };
                __decorate([
                    core_1.Input(), 
                    __metadata('design:type', Object)
                ], TabContentComponent.prototype, "tab", void 0);
                TabContentComponent = __decorate([
                    core_1.Component({
                        selector: 'tab-content',
                        templateUrl: 'js/components/tab-content/tab-content.html'
                    }), 
                    __metadata('design:paramtypes', [tab_content_service_1.TabContentService])
                ], TabContentComponent);
                return TabContentComponent;
            })();
            exports_1("TabContentComponent", TabContentComponent);
        }
    }
});

//# sourceMappingURL=tab-content.component.js.map
