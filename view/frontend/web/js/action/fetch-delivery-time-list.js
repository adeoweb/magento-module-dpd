define(['jquery', 'mage/url'], function($, UrlBuilder) {
    return {
        fetch: function(city) {
            let url = UrlBuilder.build('rest/V1/dpd/delivery-time', {});

            return $.ajax({
                url: url,
                cache: true,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({city: city}),
                dataType: 'json'
            });
        }
    };
});