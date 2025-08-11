<div class="btn-group" role="group">
    <button type="button"
            class="btn btn-info btn-sm"
            title="View Student Details"
            onclick="showStudentDetails({{ $student->id }})">
        <i class="fas fa-eye"></i>
    </button>
    <a href="{{ route('admin.students.edit', $student) }}"
       class="btn btn-warning btn-sm"
       title="Edit Student">
        <i class="fas fa-edit"></i>
    </a>
    <button type="button"
            class="btn btn-danger btn-sm"
            title="Delete Student"
            onclick="deleteStudent({{ $student->id }}, '{{ $student->fname }} {{ $student->lname }}')">
        <i class="fas fa-trash"></i>
    </button>
</div>

<script>
function deleteStudent(studentId, studentName) {
    if (confirm('Are you sure you want to delete student: ' + studentName + '?')) {
        // Create a form and submit it
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/students/' + studentId;

        // Add CSRF token
        var csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);

        // Add method spoofing for DELETE
        var methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
