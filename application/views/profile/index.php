<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? html_escape($title) : 'My Profile'; ?></title>
</head>
<body>
    <h1>My Profile</h1>

    <p><a href="<?= site_url('auth/dashboard'); ?>">Back to Dashboard</a></p>

    <?php if ($this->session->flashdata('success_message')): ?>
        <div style="color: green; margin-bottom: 15px;">
            <?= html_escape($this->session->flashdata('success_message')); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div style="color: red; margin-bottom: 15px;">
            <?= html_escape($error_message); ?>
        </div>
    <?php endif; ?>

    <?php if (validation_errors()): ?>
        <div style="color: red; margin-bottom: 15px;">
            <?= validation_errors(); ?>
        </div>
    <?php endif; ?>

    <?= form_open_multipart('profile/save'); ?>

        <p>
            <label for="headline">Professional Headline</label><br>
            <input
                type="text"
                name="headline"
                id="headline"
                maxlength="255"
                value="<?= set_value('headline', isset($profile->headline) ? $profile->headline : ''); ?>"
            >
        </p>

        <p>
            <label for="biography">Biography</label><br>
            <textarea
                name="biography"
                id="biography"
                rows="6"
                cols="60"
            ><?= set_value('biography', isset($profile->biography) ? $profile->biography : ''); ?></textarea>
        </p>

        <p>
            <label for="linkedin_url">LinkedIn Profile URL</label><br>
            <input
                type="url"
                name="linkedin_url"
                id="linkedin_url"
                maxlength="255"
                value="<?= set_value('linkedin_url', isset($profile->linkedin_url) ? $profile->linkedin_url : ''); ?>"
            >
        </p>

        <p>
            <label for="profile_image">Profile Image</label><br>
            <input type="file" name="profile_image" id="profile_image" accept=".jpg,.jpeg,.png">
        </p>

        <?php if (!empty($profile->profile_image)): ?>
            <p>Current Image:</p>
            <img
                src="<?= base_url($profile->profile_image); ?>"
                alt="Profile Image"
                style="max-width: 180px; height: auto;"
            >
        <?php endif; ?>

        <p>
            <button type="submit">Save Profile</button>
        </p>

    <?= form_close(); ?>

    <hr>

    <h2>Degrees</h2>

    <?php if (!empty($degree_error)): ?>
        <div style="color: red; margin-bottom: 15px;">
            <?= $degree_error; ?>
        </div>
    <?php endif; ?>

    <h3>Add Degree</h3>

    <?= form_open('profile/add_degree'); ?>

        <p>
            <label for="degree_name">Degree Name</label><br>
            <input type="text" name="degree_name" id="degree_name" value="<?= set_value('degree_name'); ?>">
        </p>

        <p>
            <label for="institution_name">Institution Name</label><br>
            <input type="text" name="institution_name" id="institution_name" value="<?= set_value('institution_name'); ?>">
        </p>

        <p>
            <label for="degree_url">Official Degree Page URL</label><br>
            <input type="url" name="degree_url" id="degree_url" value="<?= set_value('degree_url'); ?>">
        </p>

        <p>
            <label for="completion_date">Completion Date</label><br>
            <input type="date" name="completion_date" id="completion_date" value="<?= set_value('completion_date'); ?>">
        </p>

        <p>
            <button type="submit">Add Degree</button>
        </p>

    <?= form_close(); ?>

    <h3>My Degrees</h3>

    <?php if (!empty($degrees)): ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>Degree</th>
                <th>Institution</th>
                <th>URL</th>
                <th>Completion Date</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($degrees as $degree): ?>
                <tr>
                    <td><?= html_escape($degree->degree_name); ?></td>
                    <td><?= html_escape($degree->institution_name); ?></td>
                    <td>
                        <?php if (!empty($degree->degree_url)): ?>
                            <a href="<?= html_escape($degree->degree_url); ?>" target="_blank">View</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?= !empty($degree->completion_date) ? html_escape($degree->completion_date) : '-'; ?></td>
                    <td>
                        <a href="<?= site_url('profile/edit_degree/' . $degree->id); ?>">Edit</a> |
                        <a href="<?= site_url('profile/delete_degree/' . $degree->id); ?>" onclick="return confirm('Are you sure you want to delete this degree?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No degrees added yet.</p>
    <?php endif; ?>

    <!-- Certifcate relate -->

    <hr>

    <h2>Professional Certifications</h2>

    <?php if (!empty($certification_error)): ?>
        <div style="color: red; margin-bottom: 15px;">
            <?= $certification_error; ?>
        </div>
    <?php endif; ?>

    <h3>Add Certification</h3>

    <?= form_open('profile/add_certification'); ?>

        <p>
            <label for="certification_name">Certification Name</label><br>
            <input type="text" name="certification_name" id="certification_name" value="<?= set_value('certification_name'); ?>">
        </p>

        <p>
            <label for="issuing_organization">Issuing Organization</label><br>
            <input type="text" name="issuing_organization" id="issuing_organization" value="<?= set_value('issuing_organization'); ?>">
        </p>

        <p>
            <label for="certification_url">Course / Certification Page URL</label><br>
            <input type="url" name="certification_url" id="certification_url" value="<?= set_value('certification_url'); ?>">
        </p>

        <p>
            <label for="certification_completion_date">Completion Date</label><br>
            <input type="date" name="certification_completion_date" id="certification_completion_date" value="<?= set_value('certification_completion_date'); ?>">
        </p>

        <p>
            <button type="submit">Add Certification</button>
        </p>

    <?= form_close(); ?>

    <h3>My Certifications</h3>

    <?php if (!empty($certifications)): ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>Certification</th>
                <th>Issuing Organization</th>
                <th>URL</th>
                <th>Completion Date</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($certifications as $certification): ?>
                <tr>
                    <td><?= html_escape($certification->certification_name); ?></td>
                    <td><?= html_escape($certification->issuing_organization); ?></td>
                    <td>
                        <?php if (!empty($certification->certification_url)): ?>
                            <a href="<?= html_escape($certification->certification_url); ?>" target="_blank">View</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?= !empty($certification->completion_date) ? html_escape($certification->completion_date) : '-'; ?></td>
                    <td>
                        <a href="<?= site_url('profile/edit_certification/' . $certification->id); ?>">Edit</a> |
                        <a href="<?= site_url('profile/delete_certification/' . $certification->id); ?>" onclick="return confirm('Are you sure you want to delete this certification?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No certifications added yet.</p>
    <?php endif; ?>

    <!-- Licence Section -->

    <hr>

    <h2>Professional Licences</h2>

    <?php if (!empty($licence_error)): ?>
        <div style="color: red; margin-bottom: 15px;">
            <?= $licence_error; ?>
        </div>
    <?php endif; ?>

    <h3>Add Licence</h3>

    <?= form_open('profile/add_licence'); ?>

        <p>
            <label for="licence_name">Licence Name</label><br>
            <input type="text" name="licence_name" id="licence_name" value="<?= set_value('licence_name'); ?>">
        </p>

        <p>
            <label for="issuing_body">Issuing Body</label><br>
            <input type="text" name="issuing_body" id="issuing_body" value="<?= set_value('issuing_body'); ?>">
        </p>

        <p>
            <label for="licence_url">Licence Awarding Body URL</label><br>
            <input type="url" name="licence_url" id="licence_url" value="<?= set_value('licence_url'); ?>">
        </p>

        <p>
            <label for="licence_completion_date">Completion Date</label><br>
            <input type="date" name="licence_completion_date" id="licence_completion_date" value="<?= set_value('licence_completion_date'); ?>">
        </p>

        <p>
            <button type="submit">Add Licence</button>
        </p>

    <?= form_close(); ?>

    <h3>My Licences</h3>

    <?php if (!empty($licences)): ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>Licence</th>
                <th>Issuing Body</th>
                <th>URL</th>
                <th>Completion Date</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($licences as $licence): ?>
                <tr>
                    <td><?= html_escape($licence->licence_name); ?></td>
                    <td><?= html_escape($licence->issuing_body); ?></td>
                    <td>
                        <?php if (!empty($licence->licence_url)): ?>
                            <a href="<?= html_escape($licence->licence_url); ?>" target="_blank">View</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?= !empty($licence->completion_date) ? html_escape($licence->completion_date) : '-'; ?></td>
                    <td>
                        <a href="<?= site_url('profile/edit_licence/' . $licence->id); ?>">Edit</a> |
                        <a href="<?= site_url('profile/delete_licence/' . $licence->id); ?>" onclick="return confirm('Are you sure you want to delete this licence?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No licences added yet.</p>
    <?php endif; ?>

    <!-- Professional Cources -->

    <hr>

    <h2>Professional Courses</h2>

    <?php if (!empty($course_error)): ?>
        <div style="color: red; margin-bottom: 15px;">
            <?= $course_error; ?>
        </div>
    <?php endif; ?>

    <h3>Add Professional Course</h3>

    <?= form_open('profile/add_course'); ?>

        <p>
            <label for="course_name">Course Name</label><br>
            <input type="text" name="course_name" id="course_name" value="<?= set_value('course_name'); ?>">
        </p>

        <p>
            <label for="provider_name">Provider Name</label><br>
            <input type="text" name="provider_name" id="provider_name" value="<?= set_value('provider_name'); ?>">
        </p>

        <p>
            <label for="course_url">Course Page URL</label><br>
            <input type="url" name="course_url" id="course_url" value="<?= set_value('course_url'); ?>">
        </p>

        <p>
            <label for="course_completion_date">Completion Date</label><br>
            <input type="date" name="course_completion_date" id="course_completion_date" value="<?= set_value('course_completion_date'); ?>">
        </p>

        <p>
            <button type="submit">Add Course</button>
        </p>

    <?= form_close(); ?>

    <h3>My Professional Courses</h3>

    <?php if (!empty($courses)): ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>Course</th>
                <th>Provider</th>
                <th>URL</th>
                <th>Completion Date</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($courses as $course): ?>
                <tr>
                    <td><?= html_escape($course->course_name); ?></td>
                    <td><?= html_escape($course->provider_name); ?></td>
                    <td>
                        <?php if (!empty($course->course_url)): ?>
                            <a href="<?= html_escape($course->course_url); ?>" target="_blank">View</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?= !empty($course->completion_date) ? html_escape($course->completion_date) : '-'; ?></td>
                    <td>
                        <a href="<?= site_url('profile/edit_course/' . $course->id); ?>">Edit</a> |
                        <a href="<?= site_url('profile/delete_course/' . $course->id); ?>" onclick="return confirm('Are you sure you want to delete this course?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No professional courses added yet.</p>
    <?php endif; ?>

    <!-- Employement History -->

    <hr>

    <h2>Employment History</h2>

    <?php if (!empty($employment_error)): ?>
        <div style="color: red; margin-bottom: 15px;">
            <?= $employment_error; ?>
        </div>
    <?php endif; ?>

    <h3>Add Employment</h3>

    <?= form_open('profile/add_employment'); ?>

        <p>
            <label for="company_name">Company Name</label><br>
            <input type="text" name="company_name" id="company_name" value="<?= set_value('company_name'); ?>">
        </p>

        <p>
            <label for="job_title">Job Title</label><br>
            <input type="text" name="job_title" id="job_title" value="<?= set_value('job_title'); ?>">
        </p>

        <p>
            <label for="start_date">Start Date</label><br>
            <input type="date" name="start_date" id="start_date" value="<?= set_value('start_date'); ?>">
        </p>

        <p>
            <label for="end_date">End Date</label><br>
            <input type="date" name="end_date" id="end_date" value="<?= set_value('end_date'); ?>">
        </p>

        <p>
            <label>
                <input type="checkbox" name="is_current" value="1" <?= set_checkbox('is_current', '1'); ?>>
                This is my current job
            </label>
        </p>

        <p>
            <label for="description">Description</label><br>
            <textarea name="description" id="description" rows="4" cols="60"><?= set_value('description'); ?></textarea>
        </p>

        <p>
            <button type="submit">Add Employment</button>
        </p>

    <?= form_close(); ?>

    <h3>My Employment History</h3>

    <?php if (!empty($employment_history)): ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>Company</th>
                <th>Job Title</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Current</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($employment_history as $employment): ?>
                <tr>
                    <td><?= html_escape($employment->company_name); ?></td>
                    <td><?= html_escape($employment->job_title); ?></td>
                    <td><?= html_escape($employment->start_date); ?></td>
                    <td><?= !empty($employment->end_date) ? html_escape($employment->end_date) : '-'; ?></td>
                    <td><?= (int)$employment->is_current === 1 ? 'Yes' : 'No'; ?></td>
                    <td><?= !empty($employment->description) ? html_escape($employment->description) : '-'; ?></td>
                    <td>
                        <a href="<?= site_url('profile/edit_employment/' . $employment->id); ?>">Edit</a> |
                        <a href="<?= site_url('profile/delete_employment/' . $employment->id); ?>" onclick="return confirm('Are you sure you want to delete this employment record?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No employment history added yet.</p>
    <?php endif; ?>
</body>
</html>