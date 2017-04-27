/**
 * Created by snowgirl on 4/14/17.
 */


/**
 * Very simple client app
 * @todo split into separate classes (views, cart, user, product..)
 * @todo add error handlers
 * @todo remove code duplicates if exists
 * @todo implement promises
 * @todo cache
 */
var shopApp = function (element, config) {
    this.iniArgs(element, config);
    this.syncClient(function (exists) {
        var cachedState = this.getState();
        this.iniDOM(exists ? (cachedState ? cachedState : this.constructor.STATE_SHOP) : this.constructor.STATE_REGISTER);
    });
};

shopApp.STATE_REGISTER = 0;
shopApp.STATE_SHOP = 1;
shopApp.STATE_ORDER = 2;
shopApp.STATE_PAYED = 3;

shopApp.prototype.iniDOM = function (state, callback) {
    this.setState(state);

    if (this.isState(this.constructor.STATE_REGISTER)) {
        this.showViewRegister(callback);
    } else if (this.isState(this.constructor.STATE_SHOP)) {
        this.showViewShop(callback);
    } else if (this.isState(this.constructor.STATE_ORDER)) {
        this.showViewOrder(callback);
    } else if (this.isState(this.constructor.STATE_PAYED)) {
        this.showViewPayed(callback);
    }
};

shopApp.prototype.getState = function () {
    return parseInt(this.getCache('state'));
};
shopApp.prototype.isState = function (state) {
    return state === this.getState();
};
shopApp.prototype.setState = function (state) {
    return this.setCache('state', state);
};

shopApp.prototype.iniArgs = function (element, config) {
    this.view = $('#' + element);
    this.config = config;
    this.storagePrefix = 'shop-';

    if (this.config['isCacheProducts']) {
        this.config['isCacheProducts'] = 'products';
    }

    if (this.config['isCacheDeliveries']) {
        this.config['isCacheDeliveries'] = 'deliveries';
    }

    this.stateCallbacks = [];
};

shopApp.prototype.clearProductsCache = function () {
    return this.clearCache('products');
};

shopApp.prototype.syncClient = function (callback) {
    if (this.getClient()) {
        this.request(['user', this.getClient().id].join('/'), 'get', {}, function (code, user) {
            if ((200 == code) && user) {
                this.setClient(user);
                $.proxy(callback, this)(true);
            } else {
                this.clearClient();
                $.proxy(callback, this)(false);
            }
        });
    } else {
        $.proxy(callback, this)(false);
    }
};
shopApp.prototype.normalizeClient = function (client) {
    if (client.level) {
        client.level = parseInt(client.level);
    }

    return client;
};
shopApp.prototype.setClient = function (client) {
    return this.setCache('client', this.normalizeClient(client));
};
shopApp.prototype.getClient = function () {
    return this.getCache('client');
};
shopApp.prototype.clearClient = function () {
    return this.clearCache('client');
};
shopApp.prototype.onClientModify = function (data, callback) {
    var uri;
    var method;

    if (this.getClient()) {
        uri = ['user', this.getClient().id].join('/');
        //we do partial updates only
        method = 'patch';
    } else {
        uri = 'user';
        //create
        method = 'post';
    }

    this.request(uri, method, data, function (code, user) {
        //@todo error processing

        if ([200, 201].indexOf(code) > -1) {
            this.setClient(user);
            $.proxy(callback, this)();
        }
    });
};

shopApp.prototype.setCartProducts = function (products) {
    return this.setCache('cart', products);
};
shopApp.prototype.getCartProducts = function () {
    var output = this.getCache('cart');

    if (!output) {
        output = {};
    }

    return output;
};
shopApp.prototype.clearCart = function () {
    return this.clearCache('cart');
};
shopApp.prototype.getClientName = function () {
    var client = this.getClient();
    return [
        client.name,
        '[<span class="price">' + client['balance'] + '</span>]'
    ].join(' ');
};

shopApp.prototype.makeUri = function (path) {
    return this.config['apiEndpoint'] + '/' + path;
};

shopApp.prototype.clearStorage = function () {
    $([sessionStorage, localStorage]).each($.proxy(function (index, storage) {
        var arr = [];

        for (var i = 0; i < storage.length; i++) {
            if (this.storagePrefix == storage.key(i).substring(0, this.storagePrefix.length)) {
                arr.push(storage.key(i));
            }
        }

        for (i = 0; i < arr.length; i++) {
            storage.removeItem(arr[i]);
        }
    }, this));
};

shopApp.prototype.request = function (uri, method, data, fn, cacheKey) {
    if (cacheKey) {
        var cacheData = this.getCache(cacheKey);
        console.log('Cache Key: ', cacheKey);
        console.log('Cache Data: ', cacheData);
        if (cacheData) {
            $.proxy(fn, this)(cacheData.code, cacheData.response);
            return true;
        }
    }

    this.view.addClass('loading');

    return $.ajax({url: this.makeUri(uri), dataType: 'json', type: method, data: data})
        .always($.proxy(function (response, code) {
            this.view.removeClass('loading');

            if (response) {
                code = response.hasOwnProperty('responseJSON') ? response['responseJSON']['code'] : response['code'];
                response = response.hasOwnProperty('responseJSON') ? response['responseJSON']['body'] : response['body'];
            } else if ('nocontent' == code) {
                code = 204;
            }

            if (cacheKey) {
                this.setCache(cacheKey, {code: code, response: response});
            }

            $.proxy(fn, this)(code, response);
        }, this));
};
shopApp.prototype.normalizeView = function (className) {
    this.view.removeAttr('class').empty();

    if (this.getClient()) {

        var mapCurrentToPreviousState = {};
        mapCurrentToPreviousState[this.constructor.STATE_SHOP] = $.proxy(function () {
            this.clearStorage();
            this.iniDOM(this.constructor.STATE_REGISTER);
        }, this);
        mapCurrentToPreviousState[this.constructor.STATE_ORDER] = $.proxy(function () {
            this.iniDOM(this.constructor.STATE_SHOP);
        }, this);
        mapCurrentToPreviousState[this.constructor.STATE_PAYED] = $.proxy(function () {
            this.iniDOM(this.constructor.STATE_SHOP);
        }, this);

        var currentState = this.getCache('state');

        if (mapCurrentToPreviousState.hasOwnProperty(currentState)) {
            var $btnBack = $('<button/>', {
                type: 'button',
                text: 'Back'
            });

            $btnBack.on('click', function () {
                mapCurrentToPreviousState[currentState]();
            });

            this.view.append($btnBack);

            var $btnClear = $('<button/>', {
                type: 'button',
                text: 'Clear'
            });

            $btnClear.on('click', $.proxy(function () {
                this.clearStorage();
//            this.clearGame();
                this.iniDOM(this.constructor.STATE_REGISTER);
            }, this));

            this.view.append($btnClear);
        }
    }

    this.view.addClass(className);
};
shopApp.prototype.genProductView = function (product) {
    var $product = $('<div/>', {class: 'product', 'data-id': product.id});

    $product.append($('<img/>', {
        class: 'product-image',
        src: [this.config['imagesWebPath'], '/', product['image'], '.jpg'].join('')
    }));

    var $productInfo = $('<div/>', {class: 'product-info'});

    $productInfo.append($('<div/>', {class: 'product-name', text: product['name']}));
    $productInfo.append($('<div/>', {class: 'product-price price', text: product['price']}));

    var $rating = $('<div/>', {class: 'product-rating'});

    var i, l;

    if (product['user_mark']) {
        for (i = 0, l = this.config['ratingStarsCount']; i < l; i++) {
            $rating.append($('<span/>', {class: 'product-rating-star ' + (i + 1 == product['user_mark'] ? 'rated' : '')}));
        }
    } else {
        $rating.addClass('can-rate');

        for (i = 0, l = this.config['ratingStarsCount']; i < l; i++) {
            $rating.append($('<span/>', {class: 'product-rating-star empty'}));
        }

        $rating.on('click', '.product-rating-star', $.proxy(function (ev) {
            var $star = $(ev.target);
            var productId = $star.closest('.product').attr('data-id');
            var mark = $star.index() + 1;
            this.makeRating(productId, mark, function () {
                this.clearProductsCache();

                //fix for screen jumping
                var windowTop = $(window).scrollTop();
                this.iniDOM(this.constructor.STATE_SHOP, function () {
                    $(window).scrollTop(windowTop);
                });
            });
        }, this));
    }

    $rating.append($('<span/>', {
        class: 'product-rating-info',
        text: Number(product['rating'] > 0 ? (parseInt(product['rating']) / parseInt(product['vote_count'])) : 0).toFixed(2)
    }));

    $productInfo.append($rating);

    $productInfo.append($('<button/>', {class: 'product-buy', text: 'Add To Cart', type: 'button'}));

    $product.append($productInfo);

    return $product;
};
shopApp.prototype.genProductCartView = function (product, quantity, total) {
    var className = [this.isState(this.constructor.STATE_SHOP) ? 'small' : '', 'cart-product'].join(' ');

    var $product = $('<div/>', {class: className, 'data-id': product.id});

    $product.append($('<img/>', {
        class: className + '-image',
        src: [this.config['imagesWebPath'], '/', product['image'], '.jpg'].join('')
    }));

    var $productInfo = $('<div/>', {class: className + '-info'});

    $productInfo.append($('<div/>', {class: className + '-name', text: product['name']}));

    $productInfo.append($('<div/>', {class: className + '-total'})
        .append($('<span/>', {class: className + '-price price', text: product['price']}))
        .append($('<span/>', {class: className + '-multi-sign'}))
        .append($('<span/>', {class: className + '-quantity', text: quantity}))
        .append($('<span/>', {class: className + '-equal-sign'}))
        .append($('<span/>', {
            class: className + '-price price',
            text: total
        })));

    var $btnInc = $('<button/>', {
        class: className + '-inc',
        text: '+',
        type: 'button'
    });

    $btnInc.on('click', $.proxy(function (ev) {
        var productId = $(ev.target).closest('.cart-product').attr('data-id');
        this.incCartProduct(productId, function (products) {
            //@todo error processing

            var callback = $.proxy(function () {
                this.$cart.empty().append(this.genCartView(products, null));
            }, this);

            if (this.isState(this.constructor.STATE_ORDER)) {
                //fix for screen jumping
                var windowTop = $(window).scrollTop();
                callback();
                $(window).scrollTop(windowTop);
            } else {
                callback();
            }
        });
    }, this));

    $productInfo.append($btnInc);

    var $btnDec = $('<button/>', {
        class: className + '-dec',
        text: '-',
        type: 'button'
    });

    $btnDec.on('click', $.proxy(function (ev) {
        var productId = $(ev.target).closest('.cart-product').attr('data-id');
        this.decCartProduct(productId, function (products) {
            //@todo error processing
            this.$cart.empty().append(this.genCartView(products, null));
        });
    }, this));

    $productInfo.append($btnDec);

    $product.append($productInfo);

    return $product;
};

shopApp.prototype.showViewRegister = function () {
    this.normalizeView('pick-name register');

    var $h2 = $('<h2/>', {text: 'Reg'});

    this.view.append($h2);

    var $form = $('<form/>', {action: this.makeUri('user'), method: 'POST'});

    var defaults = {
        name: 'Customer #' + Math.floor(new Date().getTime() / 1000),
        balance: this.config['defaultBalance']
    };

    var $inputName = $('<input/>', {
        name: 'name',
        type: 'text',
        placeholder: defaults.name
    });

    $form.append($('<label/>').append($('<span/>', {text: 'Name'})).append($inputName));

    var $inputBalance = $('<input/>', {
        name: 'balance',
        type: 'number',
        placeholder: defaults.balance
    });

    $form.append($('<label/>').append($('<span/>', {text: 'Balance'})).append($inputBalance));

    var $btn = $('<button/>', {
        type: 'submit',
        text: 'OK'
    });

    $form.append($('<label/>').append($('<span/>')).append($btn));

    $form.on('submit', $.proxy(function (ev) {
        var form = objectifyForm($(ev.target).serializeArray());

        for (var name in form) {
            if (form.hasOwnProperty(name)) {
                if (0 == form[name].length) {
                    form[name] = defaults[name];
                }
            }
        }

        this.onClientModify(form, function () {
            this.iniDOM(this.constructor.STATE_SHOP);
        });

        return false;
    }, this));

    this.view.append($form);
    $inputName.focus();
};

function objectifyForm(formArray) {
    var returnArray = {};

    for (var i = 0; i < formArray.length; i++) {
        returnArray[formArray[i]['name']] = formArray[i]['value'];
    }

    return returnArray;
}

shopApp.prototype.showViewShop = function (callback) {
    this.request(['products-with-rating', this.getClient().id].join('/'), 'get', {}, function (code, products) {
        //@todo error processing
        this.normalizeView('shop');

        var $h2 = $('<h2/>', {html: '<b>' + this.getClientName() + '</b>, awesome products waintig for you!'});

        this.view.append($h2);

        var $products = $('<div/>', {class: 'products'});

        for (var id in products) {
            if (products.hasOwnProperty(id)) {
                var $product = this.genProductView(products[id]);
                $products.append($product);
            }
        }

        $products.on('click', '.product-buy', $.proxy(function (ev) {
            var productId = $(ev.target).closest('.product').attr('data-id');
            this.incCartProduct(productId, function () {
                this.$cart.empty().append(this.genCartView(products, null));
            });
        }, this));

        this.view.append($products);

        this.$cart = $('<div/>', {class: 'cart-wrapper'});

        this.$cart.append(this.genCartView(products, null));

        this.view.append(this.$cart);

        callback && $.proxy(callback, this)();
    }, this.config['isCacheProducts']);
};
shopApp.prototype.showViewOrder = function () {
    this.request(['products-with-rating', this.getClient().id].join('/'), 'get', {}, function (code, products) {
        //@todo error processing
        this.request('deliveries', 'get', {}, function (code, deliveries) {
            //@todo error processing
            this.normalizeView('order');

            var $h2 = $('<h2/>', {html: '<b>' + this.getClientName() + '</b>, here is your order!'});

            this.view.append($h2);

            this.$cart = $('<div/>', {class: 'cart-wrapper'});

            this.$cart.append(this.genCartView(products, deliveries));

            this.view.append(this.$cart);
        }, this.config['isCacheDeliveries']);
    }, this.config['isCacheProducts']);
};
shopApp.prototype.showViewPayed = function () {
    this.normalizeView('payed');

    this.view.append($('<h2/>', {html: '<b>' + this.getClientName() + '</b>, Congrats!'}));
    this.view.append($('<div/>', {
        class: 'payed-text', html: [
            'Dear ' + this.getClient().name,
            'We\'ve withdrew money for the order you\'ve made',
            'You order is in the way!',
            'Good luck!'
        ].join('<br/>')
    }));
};
shopApp.prototype.incCartProduct = function (productId, callback) {
    this.request(['products-with-rating', this.getClient().id].join('/'), 'get', {}, function (code, products) {
        //@todo error processing

        if (!products.hasOwnProperty(productId)) {
            //@todo error processing
            return false;
        }

        var cartProductIdToQuantity = this.getCartProducts();

        if (!cartProductIdToQuantity.hasOwnProperty(productId)) {
            cartProductIdToQuantity[productId] = 0;
        }

        cartProductIdToQuantity[productId]++;

        this.setCartProducts(cartProductIdToQuantity);

        $.proxy(callback, this)(products);

    }, this.config['isCacheProducts']);
};
shopApp.prototype.decCartProduct = function (productId, callback) {
    this.request(['products-with-rating', this.getClient().id].join('/'), 'get', {}, function (code, products) {
        //@todo error processing

        if (!products.hasOwnProperty(productId)) {
            //@todo error processing
            return false;
        }

        var cartProductIdToQuantity = this.getCartProducts();

        if (!cartProductIdToQuantity.hasOwnProperty(productId)) {
            cartProductIdToQuantity[productId] = 0;
        }

        cartProductIdToQuantity[productId]--;

        if (0 == cartProductIdToQuantity[productId]) {
            delete cartProductIdToQuantity[productId];
        }

        this.setCartProducts(cartProductIdToQuantity);

        $.proxy(callback, this)(products);

    }, this.config['isCacheProducts']);
};
shopApp.prototype.genCartView = function (products, deliveries) {
    var $cart = $('<div/>', {class: 'cart'});

    if (this.isState(this.constructor.STATE_SHOP)) {
        $cart.append($('<h3/>', {class: 'cart-header', text: 'Your Cart'}));
    }

    var $products = $('<div/>', {class: 'cart-products'});

    var cartProductIdToQuantity = this.getCartProducts();

    var total = 0;

    if (cartProductIdToQuantity) {
        for (var id in cartProductIdToQuantity) {
            if (cartProductIdToQuantity.hasOwnProperty(id)) {
                var tmpTotal = parseFloat(products[id]['price']) * parseInt(cartProductIdToQuantity[id]);
                total += tmpTotal;
                $products.append(this.genProductCartView(products[id], cartProductIdToQuantity[id], Number(tmpTotal).toFixed(2)))
            }
        }
    }

    $cart.append($products);

    if (total) {
        if (deliveries) {
            var $deliveries = $('<select/>', {class: 'cart-delivery'});

            $deliveries.append($('<option/>', {value: 0, text: 'Delivery'}));

            for (id in deliveries) {
                if (deliveries.hasOwnProperty(id)) {
                    $deliveries.append($('<option/>', {
                        value: id,
                        text: deliveries[id]['name'] + ' ' + deliveries[id]['price'] + '$'
                    }));
                }
            }

            $deliveries.on('change', function (ev) {
                var deliveryId = $(ev.target).val();
                if (0 != deliveryId) {
                    var price = parseFloat(deliveries[deliveryId]['price']);
                    $total.text(Number(parseFloat(total) + price).toFixed(2));
                }
            });

            $cart.append($deliveries);
        }

        var $total = $('<div/>', {class: 'cart-total price', text: 'Total: ' + Number(total).toFixed(2)});

        var isNeedMoreMoney = total > this.getClient().balance;

        if (isNeedMoreMoney) {
            $total.addClass('overdraft');
        }

        $cart.append($total);

        if (!isNeedMoreMoney) {
            var $btnMakeOrder = $('<button/>', {
                class: 'cart-order',
                type: 'button',
                text: this.isState(this.constructor.STATE_SHOP) ? 'Order' : 'Pay'
            });

            $btnMakeOrder.on('click', $.proxy(function () {
                if (this.isState(this.constructor.STATE_SHOP)) {
                    this.iniDOM(this.constructor.STATE_ORDER);
                } else {
                    var $delivery = $cart.find('.cart-delivery');

                    if (0 == $delivery.val()) {
                        var $deliveryIsEmpty = $('<span/>', {
                            class: 'cart-delivery-empty',
                            text: 'Choose delivery type please...'
                        });

                        $deliveryIsEmpty.insertAfter($delivery);

                        setTimeout(function () {
                            $deliveryIsEmpty.remove();
                        }, 2000);
                    } else {
                        this.makeOrder($delivery.val(), function () {
                            this.clearCart();
                            this.iniDOM(this.constructor.STATE_PAYED);
                        });
                    }
                }
            }, this));

            $cart.append($btnMakeOrder);
        }
    } else {
        $products.append($('<div/>', {class: 'cart-empty', text: 'Empty'}));
    }

    return $cart;
};

shopApp.prototype.makeOrder = function (deliveryId, callback) {
    var uri = ['order', this.getClient().id, 'delivery', deliveryId].join('/');

    this.request(uri, 'post', {product_id_to_quantity: this.getCartProducts()}, function (code, response) {
        //@todo error processing

        if ([200, 201].indexOf(code) > -1) {
            this.setClient(response['user']);
            $.proxy(callback, this)();
        }
    });
};

shopApp.prototype.makeRating = function (productId, mark, callback) {
    //'rating/product/{product_id}/user/{user_id}/mark/{mark}'
    var uri = ['rating', 'product', productId, 'user', this.getClient().id, 'mark', mark].join('/');

    this.request(uri, 'post', {}, function (code, response) {
        console.log(code, response);
        //@todo error processing

        if ([200, 201].indexOf(code) > -1) {
            $.proxy(callback, this)();
        }
    });
};

shopApp.prototype.setCache = function (k, v) {
    var json = JSON.stringify(v);

    k = this.storagePrefix + k;

    if (sessionStorage) {
        sessionStorage.setItem(k, json);
    }

    if (localStorage) {
        localStorage.setItem(k, json);
    }
};
shopApp.prototype.getCache = function (k) {
    k = this.storagePrefix + k;

    var v;

    if (!v) {
        if (localStorage && (v = localStorage.getItem(k))) {
            v = JSON.parse(v);
        }
    }

    if (!v) {
        if (sessionStorage && (v = sessionStorage.getItem(k))) {
            v = JSON.parse(v);
        }
    }

    if (v) {
        this[k] = v;
    }

    return v;
};
shopApp.prototype.clearCache = function (k) {
    k = this.storagePrefix + k;

    if (sessionStorage) {
        sessionStorage.removeItem(k);
    }

    if (localStorage) {
        localStorage.removeItem(k);
    }
};