
ALTER TABLE customers MODIFY (address VARCHAR2(400));


ALTER TABLE receivers MODIFY (phone VARCHAR2(20) NOT NULL);

PROMPT customers.address widened to VARCHAR2(400).
PROMPT receivers.phone confirmed NOT NULL.
