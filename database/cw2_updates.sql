ALTER TABLE degrees
ADD COLUMN programme VARCHAR(150) NULL AFTER degree_name;

ALTER TABLE employment_history
ADD COLUMN industry_sector VARCHAR(100) NULL AFTER job_title,
ADD COLUMN location VARCHAR(150) NULL AFTER industry_sector;

CREATE INDEX idx_degrees_programme ON degrees(programme);
CREATE INDEX idx_degrees_completion_date ON degrees(completion_date);
CREATE INDEX idx_employment_industry_sector ON employment_history(industry_sector);
CREATE INDEX idx_employment_location ON employment_history(location);