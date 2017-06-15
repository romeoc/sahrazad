(function($) {
    Sahrazad = {
        data: {},
        collect: function() {
            // Global info
            Sahrazad.data['special_price_low'] = window.runParams.actMinPrice;
            Sahrazad.data['special_price_high'] = window.runParams.actMaxPrice;
            Sahrazad.data['price_low'] = window.runParams.minPrice;
            Sahrazad.data['price_high'] = window.runParams.maxPrice;
            Sahrazad.data['available_quantity'] = window.runParams.totalAvailQuantity;
            Sahrazad.data['product_id'] = window.runParams.productId;
            Sahrazad.data['title'] = $('.product-name').html();
            Sahrazad.data['woodropship_score'] = $('.card-title').html();
            Sahrazad.data['ships_epacket'] = ($('#j-shipping-company').html() === 'ePacket');
            
            var variationImages = $('.item-sku-image img').map(function() { return this.src.replace('_50x50.jpg', ''); }).get();
            Sahrazad.data['gallery_images'] = window.runParams.imageBigViewURL.concat(variationImages);

            // Description Images
            Sahrazad.data['description_images'] = $(".description-content img").map(function() {
                return $(this).attr("src");
            }).get();

            // Variations
            if (Object.keys(window.skuProducts).length > 1) {
                Sahrazad.data['variations'] = [];
                for (var product in window.skuProducts) {
                    var variation = {};
                    variation['sku'] = window.skuProducts[product].skuPropIds;
                    variation['price'] = window.skuProducts[product].skuVal.skuCalPrice;
                    variation['special_price'] = window.skuProducts[product].skuVal.actSkuCalPrice;
                    variation['available_quantity'] = window.skuProducts[product].skuVal.availQuantity;
                    variation['price'] = window.skuProducts[product].skuVal.skuCalPrice;
                    variation['price'] = window.skuProducts[product].skuVal.skuCalPrice;

                    var image = $('.item-sku-image [data-sku-id="' + variation['sku'] + '"]').find('img');
                    if (image.size() > 0) {
                        variation['name'] = image.attr('title');
                        variation['image'] = image.attr('src').replace('_50x50.jpg', '');
                    }

                    Sahrazad.data['variations'].push(variation);
                }
            }

            // Attributes
            Sahrazad.data['attributes'] = [];
            $('.product-property-list li').each(function() {
                var attribute = $(this);
                var title = attribute.find('.propery-title').html();
                var value = attribute.find('.propery-des').html();

                Sahrazad.data['attributes'].push({
                    'title': title.replace(':', ''),
                    'value': value
                });
            });

            return Sahrazad.data;
        },
        enqueue: function() {
            var data = Sahrazad.collect();
            data = {'info': JSON.stringify(data)};
            
            $.post("https://sahrazad.local/public/bridge.php", data).done(function(response, status) {
                if (status === 'success') {
                    $('.sahrazad-enqueue').addClass('complete').html('Done');
                }
            });
        },
        init: function() {
            var button = $('<button type="button" class="sahrazad-enqueue">Enqueue</button>');
            $("body").append(button);

            $('.sahrazad-enqueue').on('click', function() {
                Sahrazad.enqueue();
            });
        }
    };
    
    Sahrazad.init();
}) (window.jQuery);

