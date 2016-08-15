$(function () {
  $('.closeFlashMessage').on('click', function () {
    $(this).parents('.flash-message').remove();
  });

  $('.dataTables').DataTable({
    "searching" : false,
    "bAutoWidth": false,
    "language"  : {
      "url": "/js/dataTablesRussian.json"
    }
  });
});

var testApp = {
  scheduleCreate: {
    init: function (getCourierBusyDatesUrl, getRegionTravelTimeUrl) {
      this.getRegionTravelTimeUrl = getRegionTravelTimeUrl;
      this.getCourierBusyDatesUrl = getCourierBusyDatesUrl;
      this.departureDateInput = $('#add_schedule_item_form_departureDate');
      this.departureDateInput.datepicker({
        language : 'ru',
        format   : 'yyyy-mm-dd',
        autoclose: true
      });
      let courierSelect = $('#add_schedule_item_form_courier');
      let regionSelect = $('#add_schedule_item_form_region');
      courierSelect.on('change', ()=>this.getCourierBusyDates(courierSelect.val())).trigger('change');
      $('#add_schedule_item_form_region, #add_schedule_item_form_departureDate').on('change', ()=>this.getRegionTravelTime(regionSelect.val())).trigger('change');
    },

    getCourierBusyDates: function (courierId) {
      $.ajax({
        url    : this.getCourierBusyDatesUrl,
        data   : {courierId: courierId},
        method : 'get',
        success: (dates)=>this.updateDatePicker(dates),
      });
    },

    getRegionTravelTime: function (regionId) {
      $.ajax({
        url    : this.getRegionTravelTimeUrl,
        data   : {regionId: regionId},
        method : 'get',
        success: (travelTime)=>this.updateArrivalData(travelTime),
      });
    },
    updateDatePicker   : function (dates) {
      if (dates.includes(this.departureDateInput.val())) {
        this.departureDateInput.datepicker('update', '');
      }
      this.departureDateInput.datepicker('remove')
        .datepicker({
          language     : 'ru',
          format       : 'yyyy-mm-dd',
          autoclose    : true,
          datesDisabled: dates
        });
    },
    updateArrivalData  : function (travelTime) {
      let date = this.departureDateInput.val();
      if (!!date && !!travelTime) {
        data = moment(date).add(travelTime, 'days').format("Y-MM-DD");
      } else {
        data = 'Не выбран регион или дата выезда.'
      }
      $('#arrival').html(data);
    }
  },
  scheduleList  : {
    init        : function (getScheduleListUrl) {
      $('.filter-date-picker').datepicker({
        language : 'ru',
        format   : 'yyyy-mm-dd',
        autoclose: true,
        clearBtn : true
      });
      this.dateFrom = $('#dateFrom')
        .on('change', ()=>this.updateDateTo());
      this.dateTo = $('#dateTo');

      var table = $('#schedule').DataTable({
        "ajax"         : {
          "url" : getScheduleListUrl,
          "data": {
            "extra_search": {
              "dateFrom": ()=>this.dateFrom.val(),
              "dateTo"  : ()=>this.dateTo.val(),
            }
          }
        },
        "searching"    : false,
        "bAutoWidth"   : false,
        "processing"   : true,
        "serverSide"   : true,
        "bSortCellsTop": true,
        "columns"      : [
          {
            "data"      : "id",
            "searchable": false
          },
          {
            "data"      : "region",
            "searchable": false
          },
          {
            "data"      : "departureDate",
            "searchable": false
          },
          {
            "data"      : "courier",
            "searchable": false
          },
        ],
        "language"     : {
          "url": "/js/dataTablesRussian.json"
        }
      });
      $('#dateFrom, #dateTo').on('change', ()=>table.ajax.reload());
    },
    updateDateTo: function () {
      let startDate = this.dateFrom.val();
      if (this.dateTo.val() < startDate) {
        this.dateTo.datepicker('update', '');
      }
      this.dateTo.datepicker('remove')
        .datepicker({
          language : 'ru',
          format   : 'yyyy-mm-dd',
          autoclose: true,
          clearBtn : true,
          startDate: startDate
        });
    }
  }
};