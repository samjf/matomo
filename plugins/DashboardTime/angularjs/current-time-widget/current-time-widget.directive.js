/*!
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */


/**
 * Usage:
 * <div piwik-current-time-widget>
 *
 * More advanced example
 * <div piwik-current-time-widget
 *    website-timezone-offset=''>
 *
 */

(function () {
  angular.module('piwikApp').directive('piwikCurrentTimeWidget', piwikCurrentTimeWidget);

  piwikCurrentTimeWidget.$inject = ['$interval'];

  function piwikCurrentTimeWidget($interval){
  

      return {
          restrict: 'A',
          scope: {
            websiteTimezone: '@'
          },
          require: "?ngModel",
          templateUrl: 'plugins/DashboardTime/angularjs/current-time-widget/current-time-widget.directive.html?cb=' + piwik.cacheBuster,
          controller: function($scope) {
            $scope.showOnlySite = false;

            var defaultFormat = new Intl.DateTimeFormat();
            var defaultLocale = defaultFormat.resolvedOptions().locale;

            this.defaultDateTimeFormat = {
                timeStyle: "short",
                dateStyle: "short",
            };
            var mergedFormatOptions = Object.assign({}, this.defaultDateTimeFormat, {
              timeZone: $scope.websiteTimezone
            });

            this.siteDateTimeFormat = new Intl.DateTimeFormat(defaultLocale, mergedFormatOptions);
            this.localDateTimeFormat = new Intl.DateTimeFormat(defaultLocale, this.defaultDateTimeFormat);

            this.updateTimes = function () {
              var now = new Date();
              $scope.siteDateTimeStr = this.siteDateTimeFormat.format(now);
              $scope.localDateTimeStr = this.localDateTimeFormat.format(now);
            }

            this.updateTimes();

            if($scope.siteDateTimeStr == $scope.localDateTimeStr) {
              $scope.showOnlySite = true;
            }

            $interval(this.updateTimes.bind(this), 60*1000);
            
          }
      };
  }
})();