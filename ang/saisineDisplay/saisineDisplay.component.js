(function(angular, $, _) {
  "use strict";

  /**
   * <saisine-display> : affiche les données de la Saisine sélectionnée dans un
   * champ de référence du formulaire Afform englobant (ex. champ saisine_id d'Event).
   *
   * Attributs (tous optionnels) :
   *   field-name  : nom du champ du formulaire qui contient l'id de la saisine,
   *                 tel qu'il apparaît dans Form Builder (ex. "Evenement_Saisine.saisine_id").
   *                 Si absent, le premier champ dont le nom finit par "saisine_id" est utilisé.
   *   api-entity  : entité APIv4 à interroger (défaut : "Saisine").
   *   fields      : liste de champs à afficher, séparés par des virgules ou des points-virgules.
   *                 Un titre personnalisé s'ajoute après « | » :
   *                 ex. "titre|Intitulé, date_reception, Groupe.champ|Type de saisine".
   *                 Défaut : tous les champs (sauf id), avec leur libellé CiviCRM.
   *   heading     : titre du bloc (défaut : "Données de la saisine").
   *   saisine-id  : (avancé) id fixe ou expression Angular, qui prend le pas sur le champ du formulaire.
   */
  angular.module('saisineDisplay').component('saisineDisplay', {
    bindings: {
      saisineId: '<?',
      fieldName: '@',
      apiEntity: '@',
      fields: '@',
      heading: '@',
      debug: '@'
    },
    require: {
      afFieldset: '?^^afFieldset'
    },
    templateUrl: '~/saisineDisplay/saisineDisplay.html',
    controller: function($scope, crmApi4) {
      var ctrl = this;
      var fieldDefsPromise = null;
      var requestToken = 0;

      ctrl.currentId = null;
      ctrl.loading = false;
      ctrl.error = null;
      ctrl.rows = [];

      function entityName() {
        return ctrl.apiEntity || 'Saisine';
      }

      // Normalise une valeur de champ (id, tableau, objet) en id entier, ou null.
      function toId(value) {
        if (angular.isArray(value)) {
          value = value[0];
        }
        if (value && angular.isObject(value)) {
          value = value.id;
        }
        var n = parseInt(value, 10);
        return n > 0 ? n : null;
      }

      // Valeurs des champs du fieldset Afform parent (ex. Event).
      function getFormFieldValues() {
        var fs = ctrl.afFieldset;
        if (!fs) {
          return null;
        }
        try {
          if (angular.isFunction(fs.getFieldData)) {
            return fs.getFieldData();
          }
          if (angular.isFunction(fs.getData)) {
            var data = fs.getData();
            if (angular.isArray(data)) {
              data = data[0];
            }
            return data && data.fields ? data.fields : data;
          }
        } catch (e) {
          // Ignoré : on retombera sur "aucune saisine sélectionnée".
        }
        return null;
      }

      ctrl.getSelectedId = function() {
        if (angular.isDefined(ctrl.saisineId) && ctrl.saisineId !== null && ctrl.saisineId !== '') {
          return toId(ctrl.saisineId);
        }
        var values = getFormFieldValues();
        if (!values) {
          return null;
        }
        var key = ctrl.fieldName;
        if (!key) {
          // Détection automatique : premier champ du formulaire dont le nom contient « saisine »
          // (ex. Caracteristiques.saisine_id ou Caracteristiques.id_de_saisine).
          key = Object.keys(values).filter(function(k) {
            return /saisine/i.test(k);
          })[0];
        }
        return key ? toId(values[key]) : null;
      };

      // Mode diagnostic (attribut debug="true") : montre ce que le formulaire expose.
      function debugSnapshot() {
        var fs = ctrl.afFieldset;
        var methods = [];
        if (fs) {
          for (var k in fs) {
            if (angular.isFunction(fs[k])) {
              methods.push(k);
            }
          }
        }
        var values = getFormFieldValues();
        var text;
        try {
          text = JSON.stringify(values, function(key, val) {
            return (typeof val === 'string' && val.length > 60) ? val.substring(0, 60) + '…' : val;
          }, 1);
        } catch (e) {
          text = '(valeurs non sérialisables)';
        }
        return 'fieldset trouvé : ' + (fs ? 'oui' : 'NON') +
          '\nméthodes du fieldset : ' + (methods.join(', ') || '(aucune)') +
          '\nid détecté : ' + ctrl.getSelectedId() +
          '\nvaleurs des champs : ' + text;
      }

      // Définitions des champs de l'entité (chargées une seule fois).
      function loadFieldDefs() {
        if (!fieldDefsPromise) {
          fieldDefsPromise = crmApi4(entityName(), 'getFields', {
            select: ['name', 'label', 'data_type', 'options', 'type'],
            loadOptions: false
          });
        }
        return fieldDefsPromise;
      }

      // Analyse l'attribut fields : "nom|Titre personnalisé, autre_nom, ..."
      // Séparateurs entre champs : virgule ou point-virgule. Retourne [{name, label}].
      function requestedFields() {
        return (ctrl.fields || '').split(/[,;]/).map(function(s) {
          var parts = s.split('|');
          var name = parts[0].trim();
          var label = parts.length > 1 ? parts.slice(1).join('|').trim() : '';
          return {name: name, label: label};
        }).filter(function(f) {
          return f.name.length > 0;
        });
      }

      // Liste des champs à afficher : [{name, label, data_type, expr}]
      function chooseFields(defs) {
        var usable = (defs || []).filter(function(d) {
          return d.type === 'Field' || d.type === 'Custom' || angular.isUndefined(d.type);
        });
        var wanted = requestedFields();
        var chosen;
        if (wanted.length) {
          chosen = wanted.map(function(w) {
            var def = usable.filter(function(d) {
              return d.name === w.name;
            })[0];
            def = angular.extend({}, def || {name: w.name, label: w.name, data_type: 'String', options: false});
            if (w.label) {
              def.label = w.label;
            }
            return def;
          });
        } else {
          chosen = usable.filter(function(d) {
            return d.name !== 'id';
          });
        }
        return chosen.map(function(d) {
          var expr = d.name;
          if (d.options && expr.indexOf(':') === -1) {
            expr += ':label';
          }
          return {name: d.name, label: d.label || d.name, data_type: d.data_type, expr: expr};
        });
      }

      function formatValue(def, value) {
        if (value === null || angular.isUndefined(value) || value === '') {
          return null;
        }
        if (angular.isArray(value)) {
          value = value.join(', ');
        }
        if (def.data_type === 'Boolean') {
          return (value === true || value === 1 || value === '1') ? 'Oui' : 'Non';
        }
        if (def.data_type === 'Date' || def.data_type === 'Timestamp') {
          var m = /^(\d{4})-(\d{2})-(\d{2})(?:[ T](\d{2}):(\d{2}))?/.exec(String(value));
          if (m) {
            var out = m[3] + '/' + m[2] + '/' + m[1];
            if (m[4] && !(m[4] === '00' && m[5] === '00')) {
              out += ' ' + m[4] + ':' + m[5];
            }
            return out;
          }
        }
        if (angular.isObject(value)) {
          return JSON.stringify(value);
        }
        return String(value);
      }

      function load(id) {
        var token = ++requestToken;
        ctrl.currentId = id;
        ctrl.error = null;
        if (!id) {
          ctrl.rows = [];
          ctrl.loading = false;
          return;
        }
        ctrl.loading = true;
        loadFieldDefs().then(function(defs) {
          var chosen = chooseFields(defs);
          var select = ['id'].concat(chosen.map(function(c) {
            return c.expr;
          }));
          return crmApi4(entityName(), 'get', {
            select: select,
            where: [['id', '=', id]],
            limit: 1
          }).then(function(result) {
            return {chosen: chosen, record: result && result[0]};
          });
        }).then(function(res) {
          if (token !== requestToken) {
            return;
          }
          ctrl.loading = false;
          if (!res.record) {
            ctrl.rows = [];
            ctrl.error = 'Saisine introuvable ou accès refusé.';
            return;
          }
          ctrl.rows = res.chosen.map(function(c) {
            return {label: c.label, value: formatValue(c, res.record[c.expr])};
          }).filter(function(row) {
            return row.value !== null;
          });
        }, function(err) {
          if (token !== requestToken) {
            return;
          }
          fieldDefsPromise = null;
          ctrl.loading = false;
          ctrl.rows = [];
          ctrl.error = (err && (err.error_message || err.message)) || 'Erreur lors du chargement de la saisine.';
        });
      }

      ctrl.$onInit = function() {
        // Recharge à chaque changement de la saisine sélectionnée dans le formulaire.
        $scope.$watch(ctrl.getSelectedId, function(id) {
          if (id !== ctrl.currentId) {
            load(id);
          }
        });
        if (ctrl.debug === 'true') {
          $scope.$watch(debugSnapshot, function(text) {
            ctrl.debugInfo = text;
          });
        }
      };
    }
  });

})(angular, CRM.$, CRM._);
