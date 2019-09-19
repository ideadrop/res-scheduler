$(document).ready(function () {
    var tokenDataEngine = new Bloodhound({
        remote: {
            url: "/getSkills?query=%QUERY",
            wildcard: "%QUERY",
            filter: function (response) {
                return response.items;
            }
        },
        datumTokenizer: function (tag) {
//            console.log(d);
            return Bloodhound.tokenizers.whitespace(tag.label);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace
    });

    tokenDataEngine.initialize();

    $('input#user-skills').tokenfield({
        createTokensOnBlur: true,
        typeahead: [null, {
                name: "user-skills",
                templates: {
                    suggestion: function (data) {
                        return '<div>' + data.label + '</div>';
                    }
                },
                display: 'label',
                source: tokenDataEngine.ttAdapter()
            }],
        beautify: false,
        delimiter: [','],
        showAutocompleteOnFocus: true
    }).on('tokenfield:createtoken', function (e) {
        var re = /^[a-zA-Z0-9]*$/;
        var valid = re.test(e.attrs.name);
        if (!valid) {
            e.preventDefault();
            $('.skill-error-message').remove();
            $(this).parent().append('<span class="help-block alert-danger skill-error-message">special character is not allowed</span>');
        }
        var existingTokens = $(this).tokenfield('getTokens');
        $.each(existingTokens, function (index, token) {
            if (token.value === e.attrs.value)
                e.preventDefault();
        });
    });
});