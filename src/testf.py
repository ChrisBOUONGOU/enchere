import smtplib
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText

#setup de la librairie
s = smtplib.SMTP_SSL("smtp.gmail.com", 465)


# me == my email address
# you == recipient's email address
me = "mail.celuiquienvoye@sender.com"
you = "exemple@email.com"

# Create message container - the correct MIME type is multipart/alternative.
msg = MIMEMultipart('alternative')
msg['Subject'] = "voici votre nouveau formulaire de contact"
msg['From'] = me
msg['To'] = you

# Create the body of the message (a plain-text and an HTML version).
text = "salut"
html = f"""  html ici  """

# Record the MIME types of both parts - text/plain and text/html.
part1 = MIMEText(text, 'plain')
part2 = MIMEText(html, 'html')

# Attach parts into message container.
# According to RFC 2046, the last part of a multipart message, in this case
# the HTML message, is best and preferred.
msg.attach(part1)
msg.attach(part2)


# Send the message via local SMTP server.
s.login("mail.celuiquienvoye@sender.com", "code ici")
# sendmail function takes 3 arguments: sender's address, recipient's address
# and message to send - here it is sent as one string.
s.sendmail(me, you, msg.as_string())
s.quit()