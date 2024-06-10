define([
    'jquery',
    'jquery/ui'
    ], function(jQuery){
        jQuery.widget('mage.sellerTreeCheckboxCategory', {
            options: {
            },
            /**
             * Widget initialization
             * @private
             */
             _create: function() {
                 self = this;
                 jQuery("#seller-category-tree").trigger('contentUpdated');
                setTimeout(() => {
                    var fromDb = self.options.fromDb;
                    var categories = [];
                    if (fromDb)  {
                        categories = fromDb.split(",");
                    }
                    jQuery("#category_id").val(categories.join(","));
                    jQuery("body").on("change", ".categories", function () {
                        if (jQuery(this).is(":checked"))  {
                            categories.push(jQuery(this).val());
                        } else  {
                            categories.splice(categories.indexOf(jQuery(this).val()), 1);
                        }
                        
                        jQuery("#category_id").val(categories.join(","));
                    });
                    var tree;

                    Ext.lib.Event.getTarget = function(e) {
                        var ee = e.browserEvent || e;
                        return ee.target ? Event.element(ee) : null;
                    };

                    Ext.tree.TreePanel.Enhanced = function(el, config)  {
                        Ext.tree.TreePanel.Enhanced.superclass.constructor.call(this, el, config);
                    };
                    Ext.extend(Ext.tree.TreePanel.Enhanced, Ext.tree.TreePanel, {
                        loadTree : function(config, firstLoad)  {
                            var parameters = config["parameters"];
                            var data = config["data"];
                            var root = new Ext.tree.TreeNode(parameters);
                            this.nodeHash = {};
                            this.setRootNode(root);
                            if (firstLoad) {
                                this.addListener("click", this.categoryClick.createDelegate(this));
                            }
                            this.loader.buildCategoryTree(root, data);
                            this.el.dom.innerHTML = "";
                            this.render();
                        },

                        categoryClick : function(node, e)   {
                            node.getUI().check(!node.getUI().checked());
                        }
                    });

                    jQuery(function()   {
                        var categoryLoader = new Ext.tree.TreeLoader({
                            dataUrl: self.options.dataUrl
                        });

                        categoryLoader.createNode = function(config) {
                            config.uiProvider = Ext.tree.CheckboxNodeUI;
                            var node;
                            var _node = Object.clone(config);
                            if (config.children && !config.children.length) {
                                delete(config.children);
                                node = new Ext.tree.AsyncTreeNode(config);
                            } else {
                                node = new Ext.tree.TreeNode(config);
                            }
                            return node;
                        };
                        categoryLoader.processResponse = function (response, parent, callback) {
                            var config = JSON.parse(response.responseText);

                            this.buildCategoryTree(parent, config);

                            if (typeof callback == "function") {
                                callback(this, parent);
                            }
                        };
                        categoryLoader.buildCategoryTree = function(parent, config)     {
                            if (!config) return null;
                            if (parent && config && config.length){
                                for (var i = 0; i < config.length; i++) {
                                    config[i].uiProvider = Ext.tree.CheckboxNodeUI;
                                    var node;
                                    var _node = Object.clone(config[i]);
                                    
                                    if (_node.children && !_node.children.length) {
                                        delete(_node.children);
                                        node = new Ext.tree.AsyncTreeNode(_node);
                                    } else {
                                        node = new Ext.tree.TreeNode(config[i]);
                                    }
                                    parent.appendChild(node);
                                    node.loader = node.getOwnerTree().loader;
                                    if (_node.children) {
                                        this.buildCategoryTree(node, _node.children);
                                    }
                                }
                            }
                        };

                        categoryLoader.buildHash = function(node)   {
                            var hash = {};
                            hash = this.toArray(node.attributes);
                            if (node.childNodes.length>0 || (node.loaded==false && node.loading==false)) {
                                hash["children"] = new Array;
                                for (var i = 0, len = node.childNodes.length; i < len; i++) {
                                    if (!hash["children"]) {
                                        hash["children"] = new Array;
                                    }
                                    hash["children"].push(this.buildHash(node.childNodes[i]));
                                }
                            }
                            return hash;
                        };

                        categoryLoader.toArray = function(attributes) {
                            var data = {form_key: FORM_KEY};
                            
                            for (var key in attributes) {
                                var value = attributes[key];
                                data[key] = value;
                            }
                            return data;
                        };

                        categoryLoader.on("beforeload", function(treeLoader, node) {
                            treeLoader.baseParams.id = node.attributes.id;
                            treeLoader.baseParams.form_key = FORM_KEY;
                        });

                        categoryLoader.on("load", function(treeLoader, node, config) {
                            varienWindowOnload();
                        });
                        tree = new Ext.tree.TreePanel.Enhanced(self.options.divId, {
                            animate:          false,
                            loader:           categoryLoader,
                            enableDD:         false,
                            containerScroll:  true,
                            selModel:         new Ext.tree.CheckNodeMultiSelectionModel(),
                            rootVisible:      self.options.rootVisible,
                            useAjax:          self.options.useAjax,
                            currentNodeId:    self.options.currentNodeId,
                            addNodeTo:        false,
                            rootUIProvider:   Ext.tree.CheckboxNodeUI
                    
                        });

                        // set the root node
                        var parameters = {
                            text:        self.options.text,
                            draggable:   false,
                            checked:     self.options.checked,
                            uiProvider:  Ext.tree.CheckboxNodeUI,
                            allowDrop:   self.options.allowDrop,
                            id:          self.options.id,
                            expanded:    self.options.expanded,
                            category_id: self.options.category_id
                        };
                        tree.loadTree({parameters:parameters, data:self.options.treeJson},true);

                    });
                }, 200);

            }
        });

    return jQuery.mage.sellerTreeCheckboxCategory;
});