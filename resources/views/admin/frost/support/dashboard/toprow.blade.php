<?php
    $totalClasses = 0;
    $totalStudents = 0;
    $activeClasses = "";
    $newStudents = 0;
?>

<div class="row">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-info elevation-1">
                <i class="fas fa-chalkboard-teacher"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Total Classes</span>
                <span class="info-box-number">
                    {{ $totalClasses }}
                </span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-danger elevation-1">
                <i class="fas fa-users"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Total Students</span>
                <span class="info-box-number">{{ $totalStudents }}</span>
            </div>
        </div>
    </div>

    <div class="clearfix hidden-md-up"></div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1">
                <i class="fas fa-chalkboard"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Active Classes</span>
                <span class="info-box-number">{{ $activeClasses }}</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-warning elevation-1">
                <i class="fas fa-user-graduate"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">New Students</span>
                <span class="info-box-number">{{ $newStudents }}</span>
            </div>
        </div>
    </div>
</div>
