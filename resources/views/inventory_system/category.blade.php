<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
        @vite(['resources/css/app.css',
    'resources/js/app.js',
    'resources/js/menudropdown.js',
    'resources/js/header.js',
    'resources/css/profile.css',
    'resources/js/patient/add-patient.js',
    'resources/css/patient/record.css',
    'resources/js/record/record.js'])
</head>
<body>
    <div class="min-vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="flex-grow-1">
                <header class=" d-flex align-items-center pe-3 ">
                    <button class="btn  d-lg-block fs-6 mx-1" id="toggleSidebar" style="z-index: 100;">
                        <i class="fa-solid fa-bars fs-2"></i>
                    </button>
                    <nav class="d-flex justify-content-between align-items-center w-100 ">
                        <h1 class="mb-0"> MEDICINE INVENTORY</span></h1>
                        <div class="profile-con position-relative justify-content-space d-flex align-items-center gap-2" style="min-width: 150px;">
                            <img src="{{ optional(Auth::user()->nurses)->profile_image 
                        ? asset(optional(Auth::user()->nurses)->profile_image) 
                        : (optional(Auth::user()->staff)->profile_image 
                            ? asset(optional(Auth::user()->staff)->profile_image) 
                            : asset('images/default_profile.png')) }}" alt="profile picture" class="profile-img" id="profile_img">
                            <div class="username-n-role">
                                <h5 class="mb-0">{{ optional(Auth::user()->nurses)->full_name
                                                    ?? optional(Auth::user()->staff)->full_name
                                                    ?? 'none'  }}</h5>
                                <h6 class="mb-0 text-muted fw-light">{{Auth::user() -> role ?? 'none';}}</h6>
                            </div>
                            <div class="links position-absolute flex-column top-20 w-100 bg-white" id="links">
                                <a href="{{ route('page.profile') }}" class="text-decoration-none text-black">view profile</a>
                                <a href="{{route('logout')}}" class="text-decoration-none text-black">Logout</a>
                            </div>
                        </div>
                    </nav>
                </header>
            <main class="d-flex flex-column container-fluid bg-light ">
                <div class="m-3 p-3 shadow min-vh-100">
                    <h2 class="mb-5 fs-1 text-center">Medicine Inventory</h2>
                    <div class="medicine-inventory d-flex gap-3 align-items-none align-items-sm-end flex-wrap flex-column flex-sm-row">
                        <div class="flex-fill">
                            <label for="" class="form-label">Show</label>
                            <input type="text" class="form-control" name="show">
                        </div>
                        <div class="flex-fill">
                            <label for="search" class="form-label">Search</label>
                            <input type="search" class="form-control">
                        </div>
                        <div class="flex-fill">
                            <label for="" class="form-label">Filter</label>
                            <select name="" class="form-select" id="">
                                <option value="">Amoxicillin</option>
                                <option value="">Paracetamol</option>
                                <option value="">Diatabs</option>
                            </select>
                        </div>
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCategoryModal"><i class="fa-solid fa-plus pe-1"></i>Add Medicine</button>
                    </div>
                    <div class="table-responsive mt-5">
                        <table class="table table-hover" id="categoryTable">
                            <thead class="table-header">
                                <tr>
                                    <th scope="col" class="text-center">No.</th>
                                    <th scope="col" class="text-center">Category Name</th>
                                    <th scope="col" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <!-- <tr>
                                    <td>1</td>
                                    <td>Analsegenic</td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button class="btn bg-primary text-white"><i class="fa-solid fa-pen-to-square me-1"></i>Edit</button>
                                            <button class="btn p-0"><i class="fa-solid fa-trash text-danger fs-3"></i></button>
                                        </div>
                                    </td>
                                </tr> -->
                                <!-- dynamic td -->
                            </tbody>
                      </table>
                    </div>                    
                </div>
            </main>
              <!-- Add Category Modal -->
            <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4 shadow-lg">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="addMedicineModalLabel">
                                <i class="fa-solid fa-capsules me-2"></i> Add New Category
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <form id="addCategoryForm" action="" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Category Name</label>
                                    <input type="text" class="form-control" name="name" required>
                                     <small id="nameError" class="text-danger d-none">This category name already exists.</small>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" id="submit" class="btn btn-success">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>