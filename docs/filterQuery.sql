MariaDB [wichtelat]> select * from wp_posts where post_type = 'wish' and post_status = 'publish' limit 1 \G

*************************** 1. row ***************************
                   ID: 24806
          post_author: 1
            post_date: 2019-12-19 21:05:45
        post_date_gmt: 2019-12-19 20:05:45
         post_content:
           post_title: WICHTAUT-202
         post_excerpt:
          post_status: publish
       comment_status: closed
          ping_status: closed
        post_password:
            post_name: wichtaut-202
              to_ping:
               pinged:
        post_modified: 2019-12-19 21:05:45
    post_modified_gmt: 2019-12-19 20:05:45
post_content_filtered:
          post_parent: 0
                 guid: https://beta.wienerwichtelchallenge.at/wuensche/wichtaut-202/
           menu_order: 0
            post_type: wish
       post_mime_type:
        comment_count: 0
1 row in set (0.01 sec)


MariaDB [wichtelat]> select * from wp_postmeta where post_id = 24806 \G
*************************** 1. row ***************************
   meta_id: 439403
   post_id: 24806
  meta_key: key
meta_value: WICHTAUT-202
*************************** 2. row ***************************
   meta_id: 439404
   post_id: 24806
  meta_key: status_id
meta_value: 10017
*************************** 3. row ***************************
   meta_id: 439405
   post_id: 24806
  meta_key: status_name
meta_value: Abgeschlossen
*************************** 4. row ***************************
   meta_id: 439406
   post_id: 24806
  meta_key: link_to_jira
meta_value: https://wichtelchallenge.collabri.at/servicedesk/customer/portal/1/WICHTAUT-202
*************************** 5. row ***************************
   meta_id: 439407
   post_id: 24806
  meta_key: wichtel_id
meta_value: 178
*************************** 6. row ***************************
   meta_id: 439408
   post_id: 24806
  meta_key: wichtel_name
meta_value: Melanie G
*************************** 7. row ***************************
   meta_id: 439409
   post_id: 24806
  meta_key: wichtel_mail
meta_value: gisser.melanie@gmail.com
*************************** 8. row ***************************
   meta_id: 439410
   post_id: 24806
  meta_key: price
meta_value: < 25
*************************** 9. row ***************************
   meta_id: 439411
   post_id: 24806
  meta_key: summary
meta_value: BUCH - Nina Hagen - Bekenntnisse
*************************** 10. row ***************************
   meta_id: 439412
   post_id: 24806
  meta_key: description
meta_value: "Die Köpfe mancher Menschen sind gebaut wie ihre Füße: vorne platt und in der Mitte hohl" hat Nina Hagen einmal gesagt.
Um seinen Horizont zu erweitern wünscht sich unser Bewohner deshalb ihr Buch Bekenntnisse um sich damit die dunklen Dezemberabende zu verkürzen.
ISBN-10: 3629022723
*************************** 11. row ***************************
   meta_id: 439413
   post_id: 24806
  meta_key: reporter_mail
meta_value: andreas.wimmer@heilsarmee.at
*************************** 12. row ***************************
   meta_id: 439414
   post_id: 24806
  meta_key: recipient
meta_value: YÖ
*************************** 13. row ***************************
   meta_id: 439415
   post_id: 24806
  meta_key: address
meta_value: Tagestreff Wintergarten - Molkereistraße 2, 1020 Wien
Dienstag, Mittwoch Donnerstag von 12:00 bis 17:00 Uhr oder nach telefonischer Vereinbarung unter
01 890 17 31 8020
*************************** 14. row ***************************
   meta_id: 439416
   post_id: 24806
  meta_key: zip
meta_value: 1020
*************************** 15. row ***************************
   meta_id: 439417
   post_id: 24806
  meta_key: end_date
meta_value: 2019-12-17
*************************** 16. row ***************************
   meta_id: 439418
   post_id: 24806
  meta_key: last_wichtel_delivery_date
meta_value: 2019-12-12
*************************** 17. row ***************************
   meta_id: 439419
   post_id: 24806
  meta_key: found_wichtel_date
meta_value: 2019-12-09
*************************** 18. row ***************************
   meta_id: 439420
   post_id: 24806
  meta_key: priority
meta_value: 467
*************************** 19. row ***************************
   meta_id: 439421
   post_id: 24806
  meta_key: social_organisation_id
meta_value: 567
19 rows in set (0.00 sec)

select * from wp_posts where post_type = 'social_organisation' limit 1 \G
*************************** 1. row ***************************
                   ID: 165
          post_author: 1
            post_date: 2019-08-12 21:06:26
        post_date_gmt: 2019-08-12 19:06:26
         post_content:
           post_title: VinziPort Wien
         post_excerpt:
          post_status: publish
       comment_status: closed
          ping_status: closed
        post_password:
            post_name: vinziport-wien
              to_ping:
               pinged:
        post_modified: 2019-09-21 21:37:04
    post_modified_gmt: 2019-09-21 19:37:04
post_content_filtered:
          post_parent: 0
                 guid: https://beta.wienerwichtelchallenge.at/?post_type=social_organisation&#038;p=165
           menu_order: 0
            post_type: social_organisation
       post_mime_type:
        comment_count: 0
1 row in set (0.00 sec)

MariaDB [wichtelat]> select * from wp_postmeta where post_id = 165 \G
*************************** 1. row ***************************
   meta_id: 412
   post_id: 165
  meta_key: _edit_last
meta_value: 1
*************************** 2. row ***************************
   meta_id: 413
   post_id: 165
  meta_key: _edit_lock
meta_value: 1569094662:1
*************************** 3. row ***************************
   meta_id: 414
   post_id: 165
  meta_key: carrier
meta_value: VinziWerke Wien
*************************** 4. row ***************************
   meta_id: 415
   post_id: 165
  meta_key: field_of_action
meta_value: Materielle Sicherung
*************************** 5. row ***************************
   meta_id: 416
   post_id: 165
  meta_key: street
meta_value: Rennweg 89A
*************************** 6. row ***************************
   meta_id: 417
   post_id: 165
  meta_key: zip
meta_value: 1030
*************************** 7. row ***************************
   meta_id: 418
   post_id: 165
  meta_key: city
meta_value: Wien
*************************** 8. row ***************************
   meta_id: 419
   post_id: 165
  meta_key: map
meta_value: 48.1913327,16.39635299738,15
*************************** 9. row ***************************
   meta_id: 420
   post_id: 165
  meta_key: reachable_via
meta_value: <ul>
<li>Oberzellergasse (Linie 71)</li>
<li>St. Marx (Linie 18, 71, 74A, S-Bahn)</li>
</ul>

*************************** 10. row ***************************
   meta_id: 421
   post_id: 165
  meta_key: delivery_hours
meta_value: <p>Mo-Fr von 11:00 bis 18:30</p>

*************************** 11. row ***************************
   meta_id: 422
   post_id: 165
  meta_key: contact
meta_value: <p>Tel.: +43 (0) 1 / 41 69 341<br />
E: <a class="LnkEmail" href="mailto:vinziport@vinzi.at">vinziport@vinzi.at</a></p>

*************************** 12. row ***************************
   meta_id: 423
   post_id: 165
  meta_key: teaser
meta_value: <p>Das VinziPort am Rennweg  ist die erste <strong>Notschlafstelle</strong> in Wien <strong>für EU Bürger</strong>, da diese in öffentlichen Einrichtungen viele Jahre lang nicht aufgenommen wurden.</p>

*************************** 13. row ***************************
   meta_id: 424
   post_id: 165
  meta_key: description
meta_value: <p>Das VinziPort am Rennweg  ist die erste <strong>Notschlafstelle</strong> in Wien <strong>für EU Bürger</strong>, da diese in öffentlichen Einrichtungen viele Jahre lang nicht aufgenommen wurden.</p>
<p>55 Männer aus den unterschiedlichsten Ländern finden hier ein warmes Bett, ein Abendessen und ein Dach über dem Kopf. Im Jahr 2015 wurden 426 Gäste aus 15 EU-Ländern bei einer Auslastung von fast 100 % aufgenommen. Jährlich gibt es im VinziPort mehr als 18.000 Nächtigungen und gekochte Abendessen. Unsere Gäste kommen unter anderem aus Deutschland, Tschechien, Griechenland, Rumänien, Ungarn, Polen, Bulgarien und Litauen.</p>
<p>Ziel des VinziPort ist es EU Bürgern, die in Österreich gestrandet sind, eine Möglichkeit zu geben anzukommen, sich zu orientieren und Perspektiven für die Zukunft zu entwickeln.</p>

*************************** 14. row ***************************
   meta_id: 425
   post_id: 165
  meta_key: link
meta_value: http://www.vinzi.at/vinziport-wien/
*************************** 15. row ***************************
   meta_id: 940
   post_id: 165
  meta_key: logo
meta_value: 359
15 rows in set (0.01 sec)

MariaDB [wichtelat]> select wp_terms.term_id, wp_terms.name from wp_term_taxonomy inner join wp_terms on (wp_term_taxonomy.taxonomy = 'wish-region' and wp_terms.term_id = wp_term_taxonomy.term_id);
+---------+------------+
| term_id | name       |
+---------+------------+
|      27 | Wien       |
|      28 | Steiermark |
|      29 | Tirol      |
|      30 | Salzburg   |
|      31 | Burgenland |
+---------+------------+
5 rows in set (0.00 sec)


select * from wp_posts p  
inner join wp_term_relationships tr on 
p.post_type = 'wish' 
and tr.object_id = p.ID;

wp_term_relationships


select w.ID as id, wm_so.meta_value as organisation 
from wp_posts w 
inner join wp_postmeta wm_so on
w.post_type = 'wish' and w.post_status = 'publish' and
w.ID = wm_so.post_id  and wm_so.meta_key = 'social_organisation_id'
where w.id = 24806 \G


select 
w.ID as id, 
wm_so.meta_value as organisation,
som_zip.meta_value as zip
from wp_posts w 
inner join wp_postmeta wm_so on
w.post_type = 'wish' and w.post_status = 'publish' and
w.ID = wm_so.post_id  and wm_so.meta_key = 'social_organisation_id'
inner join wp_postmeta som_zip on
wm_so.meta_value = som_zip.post_id and som_zip.meta_key = 'zip'
where w.id = 24806 \G

select 
w.ID as id, 
wm_so.meta_value as organisation,
som_zip.meta_value as zip,
som_city.meta_value as city,
som_foa.meta_value as field_of_action
from wp_posts w 
inner join wp_postmeta wm_so on
w.post_type = 'wish' and w.post_status = 'publish' and
w.ID = wm_so.post_id  and wm_so.meta_key = 'social_organisation_id'
inner join wp_postmeta som_zip on
wm_so.meta_value = som_zip.post_id and som_zip.meta_key = 'zip'
inner join wp_postmeta som_city on
wm_so.meta_value = som_city.post_id and som_city.meta_key = 'city'
inner join wp_postmeta som_foa on
wm_so.meta_value = som_foa.post_id and som_foa.meta_key = 'field_of_action'
where w.id = 24806 \G




select 
w.ID as id, 
wm_key.meta_value as `key`,
wm_status.meta_value as status,
wm_so.meta_value as organisation,
som_zip.meta_value as zip,
som_city.meta_value as city,
som_foa.meta_value as field_of_action
from wp_posts w 
inner join wp_postmeta wm_so on
w.post_type = 'wish' and w.post_status = 'publish' and
w.ID = wm_so.post_id  and wm_so.meta_key = 'social_organisation_id'
inner join wp_postmeta wm_key on
w.ID = wm_key.post_id  and wm_key.meta_key = 'key'
inner join wp_postmeta wm_status on
w.ID = wm_status.post_id  and wm_status.meta_key = 'status_id'
inner join wp_postmeta som_zip on
wm_so.meta_value = som_zip.post_id and som_zip.meta_key = 'zip'
inner join wp_postmeta som_city on
wm_so.meta_value = som_city.post_id and som_city.meta_key = 'city'
inner join wp_postmeta som_foa on
wm_so.meta_value = som_foa.post_id and som_foa.meta_key = 'field_of_action'
where w.id = 24806 \G



select 
w.ID as id, 
wm_key.meta_value as `key`,
wm_status.meta_value as status,
wm_so.meta_value as organisation,
som_zip.meta_value as zip,
som_city.meta_value as city,
som_foa.meta_value as field_of_action
from wp_posts w 
inner join wp_postmeta wm_so on
w.post_type = 'wish' and w.post_status = 'publish' and
w.ID = wm_so.post_id  and wm_so.meta_key = 'social_organisation_id'
inner join wp_postmeta wm_key on
w.ID = wm_key.post_id  and wm_key.meta_key = 'key'
inner join wp_postmeta wm_status on
w.ID = wm_status.post_id  and wm_status.meta_key = 'status_id'
inner join wp_postmeta som_zip on
wm_so.meta_value = som_zip.post_id and som_zip.meta_key = 'zip'
inner join wp_postmeta som_city on
wm_so.meta_value = som_city.post_id and som_city.meta_key = 'city'
inner join wp_postmeta som_foa on
wm_so.meta_value = som_foa.post_id and som_foa.meta_key = 'field_of_action'
where w.id = 24806 \G