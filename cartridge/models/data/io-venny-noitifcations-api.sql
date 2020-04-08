CREATE TABLE IF NOT EXISTS	notifications	(
ID	SERIAL	,
notification_ID	VARCHAR(30)	NOT NULL UNIQUE,
notification_attributes	JSON	NULL,
notification_message	TEXT	NOT NULL,
notification_type	VARCHAR(30)	NOT NULL,
notification_opened	INT	NOT NULL,
notification_viewed	INT	NOT NULL,
notification_recipient	VARCHAR(30)	NOT NULL,
notification_sender	VARCHAR(30)	NOT NULL,
notification_subject	VARCHAR(255)	NOT NULL,
notification_object	VARCHAR(255)	NOT NULL,
profile_ID	VARCHAR(30)	NOT NULL,
app_ID	VARCHAR(30)	NOT NULL,
event_ID	VARCHAR(30)	NOT NULL,
process_ID	VARCHAR(30)	NOT NULL,
time_started	TIMESTAMPTZ	NOT NULL DEFAULT NOW(),
time_updated	TIMESTAMPTZ	NOT NULL DEFAULT NOW(),
time_finished	TIMESTAMPTZ	NOT NULL DEFAULT NOW(),
active	INT	NOT NULL DEFAULT 1
);
CREATE SEQUENCE notifications_sequence;
ALTER SEQUENCE notifications_sequence RESTART WITH 8301;
ALTER TABLE notifications ALTER COLUMN ID SET DEFAULT nextval('notifications_sequence');
-- ALTER TABLE notifications ADD FOREIGN KEY (profile_ID) REFERENCES profiles(profile_ID);
-- ALTER TABLE notifications ADD FOREIGN KEY (app_ID) REFERENCES apps(app_ID);
SELECT * FROM notifications;
DROP TABLE notifications;
INSERT INTO notifications (notification_ID,notification_attributes,notification_message,notification_type,notification_opened,notification_viewed,notification_recipient,notification_sender,notification_subject,notification_object,profile_ID,app_ID,event_ID,process_ID)		
 VALUES ('30-characters','{}','lorem ipsum','30 characters','1','1','30 characters','30 characters','255 characters','255 characters','30 characters','30 characters','30 characters','30 characters');		
SELECT * FROM notifications;