<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        @include('admin.includes.head')
        @yield('head')
    </head>
    <body>

    {{header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0")}}
    {{header("Expires: Sat, 26 Jul 1997 05:00:00 GMT")}}

    @yield('css')
        <div id="wrapper">
            @include('admin.includes.navmenu')

            @yield('content')

        </div>
        <!-- /#wrapper -->

        <!-- jQuery -->
        <script src="{!! asset("public/vendor/admin/jquery/jquery.min.js") !!}"></script>
        {{--Photo Gallary --}}
        <script src="{!! asset("public/js/admin/photo-gallery.js") !!}"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="{!! asset("public/vendor/admin/bootstrap/js/bootstrap.min.js") !!}"></script>
        <script src="{!! asset("public/vendor/admin//bootstrap/js/bootstrap-tagsinput.js/") !!}"></script>
        <!-- Metis Menu Plugin JavaScript -->
        <script src="{!! asset("public/vendor/admin/metisMenu/metisMenu.min.js") !!}"></script>

        <!-- DataTables JavaScript -->
        <script src="{!! asset("public/vendor/admin/datatables/js/jquery.dataTables.min.js") !!}"></script>
        <script src="{!! asset("public/vendor/admin/datatables-plugins/dataTables.bootstrap.min.js") !!}"></script>
        <script src="{!! asset("public/vendor/admin/datatables-responsive/dataTables.responsive.js") !!}"></script>

        <!-- Custom Theme JavaScript -->
        <script src="{!! asset("public/dist/js/sb-admin-2.js") !!}"></script>

        <!-- Admin JavaScript -->
        <script src="{!! asset("public//js/admin/admin.js") !!}"></script>

        <!-- Bootstrap Dialog -->
        <script src="{{ asset('public/dist/js/bootstrap-dialog.js')}}"></script>
        <script src="{{ asset('public//dist/js/bootstrap-dialog.min.js')}}"></script>



        <!--MultiSelect-->
        <script src="{{ asset('public/vendor/admin/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}"></script>
        <!-- Summer note -->
        <script src="{!! asset("public/dist/js/summernote.js") !!}"></script>
        <script>
            $(document).ready(function() {
                $('#summernote').summernote({
                    height: 250,
                });
            });
        </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initialize" async defer></script>
    <script>
        function initialize() {

            $('form').on('keyup keypress', function(e) {
                var keyCode = e.keyCode || e.which;
                if (keyCode === 13) {
                    e.preventDefault();
                    return false;
                }
            });
            const locationInputs = document.getElementsByClassName("map-input");

            const autocompletes = [];
            const geocoder = new google.maps.Geocoder;
            for (let i = 0; i < locationInputs.length; i++) {

                const input = locationInputs[i];
                const fieldKey = input.id.replace("-input", "");
                const isEdit = document.getElementById(fieldKey + "-latitude").value != '' && document.getElementById(fieldKey + "-longitude").value != '';

                const latitude = parseFloat(document.getElementById(fieldKey + "-latitude").value) || -33.8688;
                const longitude = parseFloat(document.getElementById(fieldKey + "-longitude").value) || 151.2195;

                const map = new google.maps.Map(document.getElementById(fieldKey + '-map'), {
                    center: {lat: latitude, lng: longitude},
                    zoom: 13
                });
                const marker = new google.maps.Marker({
                    map: map,
                    position: {lat: latitude, lng: longitude},
                });

                marker.setVisible(isEdit);

                const autocomplete = new google.maps.places.Autocomplete(input);
                autocomplete.key = fieldKey;
                autocompletes.push({input: input, map: map, marker: marker, autocomplete: autocomplete});
            }

            for (let i = 0; i < autocompletes.length; i++) {
                const input = autocompletes[i].input;
                const autocomplete = autocompletes[i].autocomplete;
                const map = autocompletes[i].map;
                const marker = autocompletes[i].marker;

                google.maps.event.addListener(autocomplete, 'place_changed', function () {
                    marker.setVisible(false);
                    const place = autocomplete.getPlace();

                    geocoder.geocode({'placeId': place.place_id}, function (results, status) {
                        if (status === google.maps.GeocoderStatus.OK) {
                            const lat = results[0].geometry.location.lat();
                            const lng = results[0].geometry.location.lng();
                            setLocationCoordinates(autocomplete.key, lat, lng);
                        }
                    });

                    if (!place.geometry) {
                        window.alert("No details available for input: '" + place.name + "'");
                        input.value = "";
                        return;
                    }

                    if (place.geometry.viewport) {
                        map.fitBounds(place.geometry.viewport);
                    } else {
                        map.setCenter(place.geometry.location);
                        map.setZoom(17);
                    }
                    marker.setPosition(place.geometry.location);
                    marker.setVisible(true);

                });
            }
        }

        function setLocationCoordinates(key, lat, lng) {
            const latitudeField = document.getElementById(key + "-" + "latitude");
            const longitudeField = document.getElementById(key + "-" + "longitude");
            latitudeField.value = lat;
            longitudeField.value = lng;
        }

    </script>

        <!--Sweet Alert-->
        <script src="{{ asset('public/dist/js/sweetalert.min.js')}}"></script>
        <!-- Include this after the sweet alert js file -->
        @include('sweet::alert')

        @if (Session::has('sweet_alert.alert'))
            <script>
                swal({!! Session::get('sweet_alert.alert') !!});
            </script>
        @endif

        @yield('script')

            </body>
        </html>
