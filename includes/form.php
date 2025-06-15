<!-- Styling for the Form -->
<style>
    /* Styling for form container */
    .container-fluid {
        max-width: 900px;
        margin: 30px auto;
        background: #f8f9fa;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }

    /* Heading */
    .container-fluid h2 {
        font-weight: 700;
        color: #343a40;
        margin-bottom: 30px;
        text-align: center;
    }

    /* Form labels */
    .form-group label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 10px;
    }

    /* Inputs */
    .form-control {
        border-radius: 8px;
        border: 1px solid #ced4da;
        padding: 10px;
        font-size: 16px;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    /* Input focus state */
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
    }

    /* Buttons */
    .btn-success {
        background-color: #28a745;
        border: none;
        font-weight: 500;
        padding: 12px 24px;
        border-radius: 5px;
        color: #fff;
    }

    .btn-secondary {
        background-color: #6c757d;
        border: none;
        font-weight: 500;
        padding: 12px 24px;
        border-radius: 5px;
        color: #fff;
        margin-left: 10px;
    }

    /* Button hover effect */
    .btn-success:hover, .btn-secondary:hover {
        opacity: 0.9;
        cursor: pointer;
    }

    /* Checkbox label */
    .form-check-label {
        font-weight: 400;
        color: #495057;
    }
</style>