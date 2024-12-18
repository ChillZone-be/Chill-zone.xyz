+++
title = 'Ihateandroid'
date = 2024-01-03T17:52:01+01:00
draft = false
author = "Mika"
comments = true  # Hier sicherstellen, dass Kommentare aktiviert sind
categories = ["Mobile", "Opinion"]
tags = ["Android", "Smartphone", "iOS", "Kritik"]
+++

I had an issue where Signal never created backups, even when I manually tried to make one.

The logs reveal the following:

```
01-16 00:54:39.725  8241  8299 I GenericForegroundServic: [startForegroundTask] Task: Creating Signal backup…, ID: 12
01-16 00:54:39.734  8241  8241 D GenericForegroundServic: [onCreate]
01-16 00:54:39.734  8241  8241 D GenericForegroundServic: [onStartCommand] Action: start
01-16 00:54:39.734  8241  8241 I GenericForegroundServic: [onStartCommand] Adding entry: ChannelId: backups_v2, ID: 12, Progress: 0/0 determinate
01-16 00:54:39.735  8241  8299 W NotificationController: Tried to update the progress, but the service was no longer bound!
01-16 00:54:39.736  8241  8241 I JobSchedulerScheduler: Waking due to job: 0
01-16 00:54:39.736  8241  8241 D GenericForegroundServic: [onBind]
01-16 00:54:39.737  8241  8241 I NotificationController: [onServiceConnected] Name: ComponentInfo{org.thoughtcrime.securesms/org.thoughtcrime.securesms.service.GenericForegroundService}
01-16 00:54:39.738  8241  8241 I GenericForegroundServic: handleReplace() ChannelId: backups_v2, ID: 12, Progress: 0/0 indeterminate
01-16 00:54:39.750  2978  3302 E MediaProvider: Creating or writing to a non-default top level directory is not allowed!
01-16 00:54:39.750  8241  8299 D NotificationController: [close] Unbinding service.
01-16 00:54:39.751  8241  8299 D GenericForegroundServic: [stopForegroundTask] ID: 12
01-16 00:54:39.752  8241  8299 I GenericForegroundServic: Stopping foreground service id=12
01-16 00:54:39.754  8241  8299 W BaseJob : [JOB::a0f417d3-7fdb-4847-ac28-55125c1f8082][LocalBackupJob] Encountered a failing exception. (Time Since Submission: 39 ms, Lifespan: Immortal, Run Attempt: 1/3, Queue: __LOCAL_BACKUP__)
01-16 00:54:39.754  8241  8299 W BaseJob : org.thoughtcrime.securesms.database.NoExternalStorageException
01-16 00:54:39.754  8241  8299 W BaseJob : 	at org.thoughtcrime.securesms.util.StorageUtil.getOrCreateBackupDirectory(StorageUtil.java:138)
01-16 00:54:39.754  8241  8299 W BaseJob : 	at org.thoughtcrime.securesms.jobs.LocalBackupJob.onRun(LocalBackupJob.java:103)
01-16 00:54:39.754  8241  8299 W BaseJob : 	at org.thoughtcrime.securesms.jobs.BaseJob.run(BaseJob.java:31)
01-16 00:54:39.754  8241  8299 W BaseJob : 	at org.thoughtcrime.securesms.jobmanager.JobRunner.run(JobRunner.java:86)
01-16 00:54:39.754  8241  8299 W BaseJob : 	at org.thoughtcrime.securesms.jobmanager.JobRunner.run(JobRunner.java:49)
01-16 00:54:39.754  8241  8299 W JobRunner: [JOB::a0f417d3-7fdb-4847-ac28-55125c1f8082][LocalBackupJob][1] Job failed. (Time Since Submission: 40 ms, Lifespan: Immortal, Run Attempt: 1/3, Queue: __LOCAL_BACKUP__)
```

After reading a couple GitHub threads and briefly checking the source code, the issue seems to be that Signal has access to the backup directory but isn't allowed to write to it for whatever reason.

After bruteforcing adb commands (and probably messing things up in the process), the following fixed the issue for me:

```bash
adb shell cmd appops set org.thoughtcrime.securesms MANAGE_EXTERNAL_STORAGE allow