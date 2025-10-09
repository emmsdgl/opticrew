**Algorithm Comparison Flow**



The purpose of the algorithm comparison of genetic algorithm with the hybrid approach of genetic algorithm + rule-based will only be within the scope of predicting the following:



1. **Workforce Size** *(would optimize the system by reducing overstaffing or understaffing.)*
2. **Employees Per Task** *(would optimize the system by workload balancing, ensuring tasks are neither under-resourced (which might cause delays) nor over-resourced (which wastes workforce capacity))*
3. **Task Allocation** (all real-time and pre-added tasks are assigned wit prioritization being applied correctly for urgent tasks)

 

**Results of the Algorithm Comparison**



**a. Prediction Accuracy** - Calculate MAE or RMSE for workforce size and task staffing predictions (lower MAE (Mean Absolute Error) or RMSE (Root Mean Squared Error) corresponds to better accuracy)



uses TaskDurationHours, EmployeeWorkSpeed, EmployeeAssigned, WorkforceSize for comparing predicted vs actual workforce and task loads.



**b. Allocation Efficiency** - Measure the percentage of tasks successfully assigned within a specific timeframe



uses TaskCompletionStatus and timestamps to measure percentage of tasks completed on time.



**c. Computational Time** - Record time taken by each system to produce its optimal solution



can be logged separately during model processing (not in dataset).



**d. Cost Efficiency** - base this metric on the salary computation and reduced average idle times, no need to consider the materials and other resources used



calculated from EmployeeHoursWorked, EmployeeHourlySalary, and TotalIdleTimeMinutes to reflect actual salary costs versus potential.



**Dataset Fields**



**With 'Task Type'**

Date,TaskID,TaskType,TaskPriority,TaskDurationHours,EmployeeID,EmployeeWorkSpeed,EmployeeAssigned,UnidentifiedEmployeesCount,TaskStartTime,TaskEndTime,IsUrgent,WorkforceSize,EmployeeHoursWorked,EmployeeHourlySalary,TotalIdleTimeMinutes,TaskCompletionStatus,RoomType,RoomSize,AssignedCleanerID



**Without 'Task Type'**

Date,TaskID,RoomType,RoomSize,TaskPriority,TaskDurationHours,EmployeeID,EmployeeWorkSpeed,EmployeeAssigned,UnidentifiedEmployeesCount,TaskStartTime,TaskEndTime,IsUrgent,WorkforceSize,EmployeeHoursWorked,EmployeeHourlySalary,TotalIdleTimeMinutes,TaskCompletionStatus,AssignedCleanerID



**CSV Schema**



**With 'Task Type'**

FieldName,DataType,Description

Date,date,Date of the cleaning task

TaskID,string,Unique identifier for each cleaning task

TaskType,string,Type/category of cleaning task (floor, window, trash, etc.)

RoomType,string,Type of room being cleaned (e.g., Office, Conference Room, Restroom)

RoomSize,float,Size of the room in square meters or square feet

TaskPriority,string,Priority of the task (High, Medium, Low)

TaskDurationHours,float,Estimated/actual duration in hours for the task

EmployeeID,string,Unique identifier for employee assigned to the task

EmployeeWorkSpeed,float,Average time in hours employee takes to complete a standardized cleaning unit

EmployeeAssigned,string,Yes/No indicating if employee is assigned to the task

UnidentifiedEmployeesCount,integer,Number of employees needed but not assigned for task

TaskStartTime,time,Task scheduled or actual start time

TaskEndTime,time,Task scheduled or actual end time

IsUrgent,string,Yes/No flag if task is urgent

WorkforceSize,integer,Total workforce available on that date

EmployeeHoursWorked,float,Number of hours employee worked in that shift/day

EmployeeHourlySalary,float,Hourly wage rate of employee

TotalIdleTimeMinutes,integer,Total idle time in minutes recorded for employee during shift

TaskCompletionStatus,string,Task status such as Completed, Pending, Cancelled

AssignedCleanerID,string,Identifier of the cleaner assigned to the room



**Without 'Task Type'**

FieldName,DataType,Description

Date,date,Date of the cleaning task

TaskID,string,Unique identifier for each cleaning task

RoomType,string,Type of room being cleaned (e.g., Office, Conference Room, Restroom)

RoomSize,float,Size of the room in square meters or square feet

TaskPriority,string,Priority of the task (High, Medium, Low)

TaskDurationHours,float,Estimated/actual duration in hours for the task

EmployeeID,string,Unique identifier for employee assigned to the task

EmployeeWorkSpeed,float,Average time in hours employee takes to complete a standardized cleaning unit

EmployeeAssigned,string,Yes/No indicating if employee is assigned to the task

UnidentifiedEmployeesCount,integer,Number of employees needed but not assigned for task

TaskStartTime,time,Task scheduled or actual start time

TaskEndTime,time,Task scheduled or actual end time

IsUrgent,string,Yes/No flag if task is urgent

WorkforceSize,integer,Total workforce available on that date

EmployeeHoursWorked,float,Number of hours employee worked in that shift/day

EmployeeHourlySalary,float,Hourly wage rate of employee

TotalIdleTimeMinutes,integer,Total idle time in minutes recorded for employee during shift

TaskCompletionStatus,string,Task status such as Completed, Pending, Cancelled

AssignedCleanerID,string,Identifier of the cleaner assigned to the room



**Tables**



1. Accounts (Clients, Employees, Employer (1))
2. Task Management (existing)
3. Analytics (Employee Performance Report, Customer Growth in terms of feedback and new accounts)



**Emma's Tables Recommendations**



1. employees (instead of skills, replace it to employee work speed + add task history, daily hours worked, idleTimes)
2. employee\_availability (as is)
3. tasks (utilize the updated table)
4. teams, team\_members, temporary\_assignments (remove teams, the task allocation will be assigned to individual employees)
5. clients (business id, customer type)
6. services (add additional location pricing)
7. service\_requests (add billing address aside from the geographical address)
8. attendance (as is)
9. employee\_requests (as is)
10. service\_feedback (as is)





Rule-based with Genetic Algorithm will be based on the following metrics:



Fitness Value

1. Fitness Score



Prediction Accuracy

1. MAE (Mean Absolute Error)
2. RMSE (Root Mean Square Error)



Convergence Rate

1. Average convergence rate



Robustness

1. Spearman Rank Correlation (quantifying the similarity of population rankings across generations)



***October 3, 2025: Chapter 2 Revisions, Chapter 3 Discussion***

***October 10: Checking of Algorithm Simulation and Chapter 3 Checking***



**Algorithm Documentations (In integration)**

Pseudocode

Algorithm Design Flow

Mathematical Formula



Functional Testing Table (revise requirements analysis column label to 'Expected Outcome')

Put Test Plan in the Appendix



**Design**

Mid-Fi Wireframe

Sticker Sheet



**Deployment**

Software Architecture



Review

measure



**Other**

Transfer Gantt Chart to Folder

Project Meeting 2 Transcription





**Algorithm Flow (Rule-based + Genetic Algorithm)**

**Genetic Algorithm (Without Prediction)**



1. **initialize population** (create a set of possible task assignments, workforce size, and scheduling)

--> **define chromosome structure** (workforce size is computed, an allocation layout is generated based on constraints)

--> **rule-based filtering** (rule-based algo is applied to further clean the assignments)

--> **diversify population**: 50% will be based on heuristic constraints ng system while 50% will be randomized

--> generate population



2\. apply rule-based to filter the generated solutions

3\. run the greedy algorithm for the filtered ones (until convergence (little or no fitness improvement over several generations)

4\. use mutation with controlled rate (Introduce random changes to workforce size, task assignments, or schedule times per iteration)

5\. employ crossover diversity (per iteration)

6\. after running the GA 10 times, calculate the fitness values of all candidate solutions and get the highest-ranking solution



**More Detailed:**



1. **Initialization** : Initialize population of potential solutions (chromosomes) where each chromosome encodes task assignments and workforce size. Define task characteristics: when, where, size/type, priority, duration, urgency
2. **Rule-Based Preprocessing** : Apply business rules to filter feasible assignments (e.g., RoomType, TaskPriority, IsUrgent, EmployeeWorkSpeed).
3. **Workforce Size Prediction**: Predict and store how many workers are needed for the task
4. **Task Allocation**: Assign specific employees to tasks (with IDs), link assignment status
5. **Employee Work Speed \& Hours**	: Evaluate employee speed for task timing and cost, track hours worked and salary for optimization
6. **Scheduling Times** : Set and adjust the start and end times for the scheduled tasks
7. **Idle Time Calculation** : Calculate and store idle time (when employee is not working) to balance and optimize scheduling
8. **Task Status Monitoring**: Track if a task is completed or pending, useful for iterative scheduling and follow-up


Algorithm Flow:
flowchart TD

%% === RULE-BASED PREPROCESSING LAYER === %%
A0([Start]) --> A1[Apply Rule-Based Checks]
A1 -->|Task within service area?| A2{Pass?}
A2 -->|No| A3([Reject Task])
A2 -->|Yes| A4[Check Certification Requirement]
A4 -->|No| A3
A4 -->|Yes| A5[Check Deadline Feasibility]
A5 -->|No| A3
A5 -->|Yes| A6([Task Accepted])

A6 --> A7([Output: Constraint-Filtered Tasks])
A7 --> B0([Proceed to Genetic Algorithm])

%% === GENETIC ALGORITHM INITIALIZATION === %%
subgraph B [Genetic Algorithm Layer]
    B0 --> B1[Define Chromosome Structure<br/>(Employee, Task, Time Slot)]
    B1 --> B2[Compute Workforce Size & Team Size per Task]
    B2 --> B3[Apply Constraints<br/>(limit by computed sizes)]
    B3 --> B4[Generate Initial Population<br/>(50% heuristic + 50% random)]
    B4 --> B5[Apply Rule-Based Validation<br/>(Skill, Time, License)]
    B5 --> B6[Evaluate Fitness<br/>(Cost, Travel, Workload, Satisfaction)]

    %% === GA LOOP === %%
    subgraph C [Evaluation & Optimization Loop]
        C1[Compute Metrics<br/>(Idle Time, Work Hours, Labor Cost, Balance)]
        C2[Selection of Parents]
        C3[Crossover (Mix Parent Schedules)]
        C4[Mutation (Introduce Random Changes)]
        C5[Compute Diversity & Fitness Improvement]
        C6{Improvement High?}
        C6 -->|Yes| C7([Output Optimized Schedule])
        C6 -->|No| C8[Regenerate / Repeat GA Loop]
        C8 --> C2
    end

    B6 --> C1
end

C7 --> Z([Final Optimized Workforce Schedule])

