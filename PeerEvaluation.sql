SELECT * from(
                (SELECT projects.projname, projects.id, relations.username
                 FROM projects
                 JOIN relations ON relations.projID = projects.id
                 WHERE relations.username="mishra14")
              UNION ALL
                (SELECT projects.projname, projects.id , projects.projname
                 FROM projects
                 WHERE projects.id NOT IN
                     (SELECT projects.id
                      FROM projects
                      JOIN relations ON relations.projID = projects.id
                      WHERE relations.username="mishra14"))nonMember);

