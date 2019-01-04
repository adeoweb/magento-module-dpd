define(['jquery', 'mage/url'], function($, UrlBuilder) {
    return {
        fetchByCountry: function(country) {
            let url = UrlBuilder.build('rest/V1/dpd/pickup-points', {});

            return $.ajax({
                url: url,
                cache: true,
                dataType: 'json',
                data: {
                    country: country
                }
            });
        }
    };
});