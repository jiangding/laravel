@extends('backend.layouts.master')
@section('content')

<!-- DataTables CSS -->
{{--<link href="{{ URL::asset('/') }}src/vendor/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">--}}
<!-- DataTables Responsive CSS -->
{{--<link href="{{ URL::asset('/') }}src/vendor/datatables-responsive/dataTables.responsive.css" rel="stylesheet">--}}

@yield("table");

<!-- DataTables JavaScript -->
{{--<script src="{{ URL::asset('/') }}src/vendor/datatables/js/jquery.dataTables.min.js"></script>--}}
{{--<script src="{{ URL::asset('/') }}src/vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>--}}
{{--<script src="{{ URL::asset('/') }}src/vendor/datatables-responsive/dataTables.responsive.js"></script>--}}

{{--<script>--}}
    {{--$(document).ready(function() {--}}
        {{--$('#dataTables-example').DataTable({--}}
            {{--responsive: true,--}}
            {{--language: {--}}
                {{--search: "搜索:"--}}
            {{--},--}}
            {{--"aoColumnDefs": [{ "bSortable": false, "aTargets": [0]}],--}}
            {{--"aaSorting": [[4, "desc"]]--}}
        {{--});--}}
    {{--});--}}
{{--</script>--}}
@endsection


