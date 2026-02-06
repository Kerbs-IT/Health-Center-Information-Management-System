<div class="records table-responsive mt-4">
    <table class="table px-3 table-hover">
        <thead class="table-header">
            <th>No</th>
            <th>Name</th>
            <th class="text-center text-nowrap">Contact Info</th>
            <th class="text-center text-nowrap">Designated Area</th>
            <th class="text-center text-nowrap">Action</th>
        </thead>
        <tbody>
            <?php $count = 1; ?>
            @foreach($healthWorker as $worker)
            <tr class="align-middle">
                <td>{{$count}}</td>
                <td>
                    <?php $image = $worker->staff->profile_image; ?>
                    <div class="d-flex gap-2 align-items-center">
                        <img src="{{ asset($image)}}" alt="health worker img" class="health-worker-img object-cover object-center">
                        <h5 class="text-nowrap">{{$worker -> staff -> full_name}}</h5>
                    </div>
                </td>
                <td class="h-100">
                    <div class="d-flex align-items-center h-100 justify-content-center">
                        <p class=" d-block mb-0">{{ optional($worker) -> staff -> contact_number ?? 'none'}}</p>
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center w-100 h-100 justify-content-center">
                        <p class="mb-0 ">{{ optional($worker) -> staff -> assigned_area -> brgy_unit ?? 'none'}}</p>
                    </div>
                </td>
                <td>
                    <div class="d-flex justify-content-center gap-0 gap-md-1">
                        <!-- Swap Area Icon -->
                        <a href="#" class="swap-icon-con d-flex align-items-center justify-content-center swap-icon"
                            data-id='{{ $worker -> id}}'
                            title="Swap Area">
                            <svg xmlns="http://www.w3.org/2000/svg" class="action-icon" viewBox="0 0 512 512" style="width: 20px; height: 20px;">
                                <!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M32 96l320 0 0-64c0-12.9 7.8-24.6 19.8-29.6s25.7-2.2 34.9 6.9l96 96c6 6 9.4 14.1 9.4 22.6s-3.4 16.6-9.4 22.6l-96 96c-9.2 9.2-22.9 11.9-34.9 6.9s-19.8-16.6-19.8-29.6l0-64L32 160c-17.7 0-32-14.3-32-32s14.3-32 32-32zM480 352c17.7 0 32 14.3 32 32s-14.3 32-32 32l-320 0 0 64c0 12.9-7.8 24.6-19.8 29.6s-25.7 2.2-34.9-6.9l-96-96c-6-6-9.4-14.1-9.4-22.6s3.4-16.6 9.4-22.6l96-96c9.2-9.2 22.9-11.9 34.9-6.9s19.8 16.6 19.8 29.6l0 64 320 0z" fill="#17a2b8" />
                            </svg>
                        </a>

                        <!-- Remove Icon -->
                        <a href="#" class="remove-icon-con d-flex align-items-center justify-content-center" data-id='{{ $worker -> id}}'>
                            <svg xmlns="http://www.w3.org/2000/svg" class="action-icon remove-icon" viewBox="0 0 384 512">
                                <path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" fill="red" />
                            </svg>
                        </a>

                        <!-- Edit Icon -->
                        <a href="#" class="edit-icon-con d-flex align-items-center justify-content-center edit-icon"
                            data-bs-toggle="modal"
                            data-bs-target="#profileModal"
                            data-id='{{ $worker -> id}}'>
                            <svg xmlns="http://www.w3.org/2000/svg" class="action-icon" viewBox="0 0 512 512">
                                <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-96c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7-14.3 32-32 32L96 448c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 64z" fill="#53c082" />
                            </svg>
                        </a>
                    </div>
                </td>
            </tr>
            <?php $count++; ?>
            @endforeach
        </tbody>
    </table>
    {{$healthWorker -> links()}}
</div>