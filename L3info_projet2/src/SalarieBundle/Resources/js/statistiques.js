/**
 * Created by jerome_skoda on 01/05/2016.
 */


// Fix for Selectize
$('#builder').on('afterCreateRuleInput.queryBuilder', function(e, rule) {
    if (rule.filter.plugin == 'selectize') {
        rule.$el.find('.rule-value-container').css('min-width', '200px')
            .find('.selectize-control').removeClass('form-control');
    }
});

var chart = null;

$('#builder').queryBuilder({
    plugins: ['bt-tooltip-errors'],
    filters: [{
        id: 'dateEntre',
        label: 'Date d\'entrée',
        type: 'date',
        validation: {
            format: 'yyyy-mm-dd'
        },
        plugin: 'datepicker',
        plugin_config: {
            format: 'yyyy-mm-dd',
            todayBtn: 'linked',
            todayHighlight: true,
            autoclose: true,
            language: __APP_REQUEST_LOCALE__
        },
        operators: ['equal', 'not_equal', 'less',  'less_or_equal', 'greater', 'greater_or_equal',  'between', 'not_between']
    }, {
        id: 'dateNaissance',
        label: 'Date de naissance',
        type: 'date',
        validation: {
            format: 'yyyy-mm-dd'
        },
        plugin: 'datepicker',
        plugin_config: {
            format: 'yyyy-mm-dd',
            todayBtn: 'linked',
            todayHighlight: true,
            autoclose: true,
            language: __APP_REQUEST_LOCALE__
        },
        operators: ['equal', 'not_equal', 'less',  'less_or_equal', 'greater', 'greater_or_equal',  'between', 'not_between']
    }, {
        id: 'salaire',
        label: 'Salaire (brut)',
        type: 'integer',
        validation: {
            min: 1466,
            max: 15992
        },
        plugin: 'slider',
        plugin_config: {
            min: 1466,
            max: 15992,
            value: 1466
        },
        valueSetter: function(rule, value) {
            if (rule.operator.nb_inputs == 1) value = [value];
            rule.$el.find('.rule-value-container input').each(function(i) {
                $(this).slider('setValue', value[i] || 0);
            });
        },
        valueGetter: function(rule) {
            var value = [];
            rule.$el.find('.rule-value-container input').each(function() {
                value.push($(this).slider('getValue'));
            });
            return rule.operator.nb_inputs == 1 ? value[0] : value;
        },
        operators: ['equal', 'not_equal', 'less',  'less_or_equal', 'greater', 'greater_or_equal',  'between', 'not_between']
    }, {
        id: 'DureeContrat',
        label: 'Durée du contrat (en mois, 0 si CDI)',
        type: 'integer',
        validation: {
            min: 0,
            max: 24
        },
        plugin: 'slider',
        plugin_config: {
            min: 0,
            max: 24,
            value: 0
        },
        valueSetter: function(rule, value) {
            if (rule.operator.nb_inputs == 1) value = [value];
            rule.$el.find('.rule-value-container input').each(function(i) {
                $(this).slider('setValue', value[i] || 0);
            });
        },
        valueGetter: function(rule) {
            var value = [];
            rule.$el.find('.rule-value-container input').each(function() {
                value.push($(this).slider('getValue'));
            });
            return rule.operator.nb_inputs == 1 ? value[0] : value;
        },
        operators: ['equal', 'not_equal', 'less',  'less_or_equal', 'greater', 'greater_or_equal',  'between', 'not_between']
    }, {
        id: 'typeContrat',
        label: 'Type de contrat',
        type: 'string',
        input: 'select',
        values: {
            'CDD' :'CDD',
            'CDI': 'CDI',
            'sta' : 'Stagiaire',
            'vol' : 'Volontaire'
        },
        operators: ['equal', 'not_equal']
    }, {
        id: 'sexe',
        label: 'Sexe',
        type: 'string',
        input: 'select',
        values: {
            'M' :'Homme',
            'F': 'Femme',
        },
        operators: ['equal', 'not_equal']
    }],
    allow_empty: true,
    rules: {condition: 'AND',
        rules: []}
});

$('.btn-reset').on('click', function() {
    $('#builder').queryBuilder('setRules', {condition: 'AND',
        rules: []});
});

$('.btn-get').on('click', function() {
    var result = $('#builder').queryBuilder('getSQL', 'named');
    $.ajax({
        method: "POST",
        url: __AJAX_URL__,
        dataType: 'json',
        data: {
            'condition': result.sql,
            'params': result.params,
            'chart-type' : $("#chart-type").val()
        }
    }).always(function( data ) {
        var ctx = $("#myChart");

        if(!chart) {
            chart = new Chart(ctx, data.chart);
        }
        else
        {
            chart.destroy();
            chart = new Chart(ctx, data.chart);
        }
    });
});